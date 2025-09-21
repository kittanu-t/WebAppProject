<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Booking;
use App\Models\SportsField;
use App\Models\FieldClosure;
use App\Models\BookingLog;

class BookingController extends Controller
{
    /**
     * แสดงรายการ booking ของ user
     */
    public function index()
    {
        $bookings = Booking::with('sportsField')
            ->where('user_id', auth()->id())
            ->orderByDesc('date')
            ->orderBy('start_time')
            ->paginate(15);

        return view('user.bookings.index', compact('bookings'));
    }

    /**
     * แสดงฟอร์มสร้าง booking ใหม่
     */
    public function create(Request $request)
    {
        $fields = SportsField::where('status', 'available')
            ->orderBy('name')
            ->get(['id','name','sport_type','min_duration_minutes','max_duration_minutes','lead_time_hours']);

        // ถ้ามาจากหน้า field detail จะมี query string ?field_id=...
        $prefield = $request->query('field_id');

        return view('user.bookings.create', compact('fields','prefield'));
    }

    /**
     * บันทึก booking ใหม่
     */
    public function store(Request $request)
    {
        // --- 1) Validate input ---
        $data = $request->validate([
            'sports_field_id' => ['required','integer','exists:sports_fields,id'],
            'date'            => ['required','date'],
            'start_time'      => ['required','date_format:H:i'],
            'end_time'        => ['required','date_format:H:i','after:start_time'],
            'purpose'         => ['nullable','string'],
            'contact_phone'   => ['nullable','string','max:30'],
        ]);

        // --- 2) โหลดข้อมูลสนาม ---
        $field = SportsField::findOrFail($data['sports_field_id']);
        if ($field->status !== 'available') {
            return back()->withErrors(['sports_field_id' => 'สนามนี้ไม่พร้อมให้จองในขณะนี้'])->withInput();
        }

        // --- 3) เตรียมเวลา ---
        $date      = Carbon::parse($data['date'])->toDateString();
        $startTime = $data['start_time'];
        $endTime   = $data['end_time'];

        $startDT = Carbon::parse("$date $startTime");
        $endDT   = Carbon::parse("$date $endTime");

        // --- 4) ตรวจเงื่อนไขธุรกิจ ---
        // 4.1 เวลาเริ่มต้องอยู่ในอนาคต และจองล่วงหน้า >= lead_time_hours
        if (now()->gte($startDT)) {
            return back()->withErrors(['start_time' => 'เวลาเริ่มต้องอยู่ในอนาคต'])->withInput();
        }
        if (now()->diffInMinutes($startDT, false) < ($field->lead_time_hours * 60)) {
            return back()->withErrors(['start_time' => "ต้องจองล่วงหน้าอย่างน้อย {$field->lead_time_hours} ชั่วโมง"])
                         ->withInput();
        }

        // 4.2 ต้องเริ่มและจบในวันเดียวกัน
        if ($startDT->toDateString() !== $endDT->toDateString()) {
            return back()->withErrors(['end_time' => 'การจองต้องอยู่ภายในวันเดียวกัน'])->withInput();
        }

        // 4.3 ความยาวการจองต้องอยู่ระหว่าง min/max
        $durationMinutes = $startDT->diffInMinutes($endDT);
        if ($durationMinutes < $field->min_duration_minutes) {
            return back()->withErrors(['end_time' => "ต้องไม่น้อยกว่า {$field->min_duration_minutes} นาที"])->withInput();
        }
        if ($durationMinutes > $field->max_duration_minutes) {
            return back()->withErrors(['end_time' => "ต้องไม่เกิน {$field->max_duration_minutes} นาที"])->withInput();
        }

        // --- 5) ตรวจชนกับ field_closures ---
        $closureBlocked = FieldClosure::where('sports_field_id', $field->id)
            ->where('start_datetime', '<', $endDT)
            ->where('end_datetime', '>', $startDT)
            ->exists();
        if ($closureBlocked) {
            return back()->withErrors(['date' => 'ช่วงเวลานี้สนามปิดให้บริการ'])->withInput();
        }

        // --- 6) ตรวจ overlap กับ booking อื่น (ไม่นับ rejected/cancelled) ---
        $overlap = Booking::where('sports_field_id', $field->id)
            ->where('date', $date)
            ->whereNotIn('status', ['cancelled','rejected'])
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
            })
            ->exists();
        if ($overlap) {
            return back()->withErrors(['start_time' => 'ช่วงเวลานี้มีการจองแล้ว'])->withInput();
        }

        // --- 7) บันทึก + Log ---
        DB::transaction(function () use ($data, $date, $startTime, $endTime, $field) {
            $booking = Booking::create([
                'user_id'         => auth()->id(),
                'sports_field_id' => $field->id,
                'date'            => $date,
                'start_time'      => $startTime . ':00',
                'end_time'        => $endTime . ':00',
                'status'          => 'pending',
                'purpose'         => $data['purpose'] ?? null,
                'contact_phone'   => $data['contact_phone'] ?? null,
            ]);

            BookingLog::create([
                'booking_id' => $booking->id,
                'action'     => 'created',
                'by_user_id' => auth()->id(),
                'note'       => null,
                'created_at' => now(),
            ]);
        });

        return redirect()->route('bookings.index')->with('status', 'ส่งคำขอจองเรียบร้อย (รออนุมัติ)');
    }

    /**
     * ยกเลิก booking ของ user
     */
    public function destroy($id)
    {
        $booking = Booking::where('user_id', auth()->id())->findOrFail($id);

        if (in_array($booking->status, ['approved','completed'])) {
            return back()->withErrors(['booking' => 'ไม่สามารถยกเลิกหลังอนุมัติ/เสร็จสิ้นแล้ว']);
        }

        DB::transaction(function () use ($booking) {
            $booking->status = 'cancelled';
            $booking->save();

            BookingLog::create([
                'booking_id' => $booking->id,
                'action'     => 'cancelled',
                'by_user_id' => auth()->id(),
                'note'       => 'cancel by user',
                'created_at' => now(),
            ]);
        });

        return back()->with('status', 'ยกเลิกการจองแล้ว');
    }
}
