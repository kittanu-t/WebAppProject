<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\SportsField;
use App\Models\FieldClosure;
use App\Models\Announcement;

class FieldController extends Controller
{
    public function schedule()
    {
        $staff = auth()->user();

        // ดึงสนามที่ staff รับผิดชอบ
        $fields = \App\Models\SportsField::where('owner_id', $staff->id)->get();

        return view('staff.fields.schedule', compact('fields'));
    }

    public function events(Request $request, $fieldId)
    {
        $staff = $request->user();

        // ตรวจสอบว่าสนามนี้เป็นของ staff จริง
        $field = \App\Models\SportsField::where('owner_id', $staff->id)->findOrFail($fieldId);

        $start = \Carbon\Carbon::parse($request->query('start'));
        $end   = \Carbon\Carbon::parse($request->query('end'));

        // bookings ในช่วงเวลา
        $bookings = \App\Models\Booking::query()
            ->where('sports_field_id', $field->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get();

        $bookingEvents = $bookings->map(function ($b) {
            $date = \Carbon\Carbon::parse($b->date)->toDateString();
            $startDT = \Carbon\Carbon::parse($date.' '.$b->start_time)->toIso8601String();
            $endDT   = \Carbon\Carbon::parse($date.' '.$b->end_time)->toIso8601String();

            $class = match ($b->status) {
                'approved'  => 'fc-booking-approved',
                'pending'   => 'fc-booking-pending',
                'completed' => 'fc-booking-completed',
                'rejected'  => 'fc-booking-rejected',
                'cancelled' => 'fc-booking-cancelled',
                default     => 'fc-booking-default',
            };

            return [
                'id'        => 'booking-'.$b->id,
                'title'     => "Booking #{$b->id} ({$b->status})",
                'start'     => $startDT,
                'end'       => $endDT,
                'className' => [$class],
            ];
        });

        // closures (ช่วงปิดสนาม)
        $closures = \App\Models\FieldClosure::query()
            ->where('sports_field_id', $field->id)
            ->where(function ($q) use ($start, $end) {
                $q->where('start_datetime', '<', $end)
                ->where('end_datetime', '>', $start);
            })
            ->get();

        $closureEvents = $closures->map(function ($c) {
            return [
                'id'        => 'closure-'.$c->id,
                'title'     => $c->reason ? 'Closed: '.$c->reason : 'Closed',
                'start'     => \Carbon\Carbon::parse($c->start_datetime)->toIso8601String(),
                'end'       => \Carbon\Carbon::parse($c->end_datetime)->toIso8601String(),
                'display'   => 'background',
                'overlap'   => false,
                'className' => ['fc-closure'],
            ];
        });

        return response()->json($bookingEvents->merge($closureEvents)->values());
    }


    // แสดงสนามที่ staff รับผิดชอบ + closure ปัจจุบัน
    public function myFields(Request $request)
    {
        $staff = $request->user();

        $fields = SportsField::with(['owner'])
            ->where('owner_id', $staff->id)
            ->orderBy('name')
            ->get();

        // หาคลอเชอร์ที่ยัง active อยู่สำหรับแต่ละสนาม
        $activeClosures = FieldClosure::whereIn('sports_field_id', $fields->pluck('id'))
            ->where('end_datetime', '>', now())
            ->orderByDesc('start_datetime')
            ->get()
            ->groupBy('sports_field_id');

        return view('staff.fields.index', compact('fields', 'activeClosures'));
    }

    // ปิดสนามชั่วคราว (สร้าง FieldClosure + เปลี่ยนสถานะ + สร้างประกาศ)
    public function close(Request $request, $fieldId)
    {
        $staff = $request->user();

        $data = $request->validate([
            'reason'        => ['required','string','max:255'],
            'end_datetime'  => ['nullable','date'], // ถ้าเว้นว่าง = ปิดจนกว่าจะเปิดเอง
            'status'        => ['nullable','in:closed,maintenance'], // โหมดปิด
        ]);

        $field = SportsField::where('owner_id', $staff->id)->findOrFail($fieldId);

        $start = now();
        $end   = !empty($data['end_datetime'])
               ? Carbon::parse($data['end_datetime'])
               : Carbon::parse('2099-12-31 23:59:59'); // ปิดยาวจนกว่าจะเปิด

        // กัน start >= end
        if ($start->gte($end)) {
            return back()->withErrors(['end_datetime' => 'วันที่สิ้นสุดต้องอยู่หลังเวลาปัจจุบัน'])->withInput();
        }

        DB::transaction(function () use ($field, $staff, $data, $start, $end) {
            // สร้าง closure
            FieldClosure::create([
                'sports_field_id' => $field->id,
                'start_datetime'  => $start,
                'end_datetime'    => $end,
                'reason'          => $data['reason'],
                'created_by'      => $staff->id,
            ]);

            // อัปเดตสถานะสนาม
            $field->status = $data['status'] ?? 'closed';
            $field->save();

            // ประกาศถึงผู้ใช้
            $title = "สนาม {$field->name} ปิดชั่วคราว";
            $period = $end->year >= 2099
                ? "ตั้งแต่ {$start} จนกว่าจะมีประกาศเปลี่ยนแปลง"
                : "ช่วง {$start} - {$end}";
            $content = "สนาม {$field->name} ปิดชั่วคราว เนื่องจาก: {$data['reason']}\n{$period}";

            Announcement::create([
                'title'        => $title,
                'content'      => $content,
                'audience'     => 'users',        // ให้ผู้ใช้เห็น
                'created_by'   => $staff->id,     // staff เป็นผู้ออกประกาศ
                'published_at' => now(),
            ]);
        });

        return back()->with('status', 'ปิดสนามชั่วคราวและสร้างประกาศเรียบร้อย');
    }

    // เปิดสนาม (ปิด closures ที่ยังค้าง + เปลี่ยนสถานะ + สร้างประกาศ)
    public function open(Request $request, $fieldId)
    {
        $staff = $request->user();

        $field = SportsField::where('owner_id', $staff->id)->findOrFail($fieldId);

        DB::transaction(function () use ($field, $staff) {
            // ปิดทุก closure ที่ยัง active ให้สิ้นสุดตอนนี้
            FieldClosure::where('sports_field_id', $field->id)
                ->where('end_datetime', '>', now())
                ->update(['end_datetime' => now()]);

            // เปิดสนาม
            $field->status = 'available';
            $field->save();

            // ประกาศ
            Announcement::create([
                'title'        => "สนาม {$field->name} เปิดให้บริการแล้ว",
                'content'      => "สนาม {$field->name} เปิดให้บริการตามปกติ ตั้งแต่ " . now(),
                'audience'     => 'users',
                'created_by'   => $staff->id,
                'published_at' => now(),
            ]);
        });

        return back()->with('status', 'เปิดสนามและสร้างประกาศเรียบร้อย');
    }
}
