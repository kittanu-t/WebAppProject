<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SportsField;
use App\Models\Booking;
use App\Models\FieldClosure;
use Carbon\Carbon;

class FieldController extends Controller
{
    public function schedule()
    {
        // หน้า Blade จะเรียก route('staff.fields.events', $fieldId) ผ่าน data-attribute
        // คุณอาจส่งรายการสนามที่เจ้าของดูแลไปเลือกในหน้า UI (ภายหลัง)
        return view('staff.fields.schedule');
    }

    /**
     * JSON events สำหรับ FullCalendar
     * GET /staff/api/fields/{field}/events?start=...&end=...
     */
    public function events(Request $request, $fieldId)
    {
        $field = SportsField::query()
            ->where('id', $fieldId)
            ->where('owner_id', auth()->id()) // กัน staff ที่ไม่ใช่เจ้าของ
            ->firstOrFail();

        // FullCalendar ส่งช่วงเวลาแบบ ISO8601; parse ช่วงให้เป็น Carbon
        // NOTE: ไม่บังคับใช้ timezone ในที่นี้ (ใช้ server timezone) — จะเพิ่มภายหลังได้
        $start = Carbon::parse($request->query('start'));
        $end   = Carbon::parse($request->query('end'));

        // ---- Bookings (approved + pending + ฯลฯ) ----
        // ดึงเฉพาะวันที่อยู่ในช่วง (performance: ใช้ idx_field_date)
        $bookings = Booking::query()
            ->where('sports_field_id', $field->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get();

        $bookingEvents = $bookings->map(function ($b) {
            // รวม date + time ให้เป็น ISO string
            $startDateTime = Carbon::parse($b->date.' '.$b->start_time)->toIso8601String();
            $endDateTime   = Carbon::parse($b->date.' '.$b->end_time)->toIso8601String();

            // สี/สไตล์—ตอนนี้ยังไม่ลง CSS มาก เดี๋ยวค่อยปรับ (ใช้ className ไว้ก่อน)
            $class = match ($b->status) {
                'approved'  => 'fc-booking-approved',
                'pending'   => 'fc-booking-pending',
                'rejected'  => 'fc-booking-rejected',
                'cancelled' => 'fc-booking-cancelled',
                'completed' => 'fc-booking-completed',
                default     => 'fc-booking-default',
            };

            return [
                'id'         => 'booking-'.$b->id,
                'title'      => 'Booked by #'.$b->user_id.' ('.$b->status.')',
                'start'      => $startDateTime,
                'end'        => $endDateTime,
                'editable'   => false,
                'className'  => [$class],
                'extendedProps' => [
                    'booking_id' => $b->id,
                    'status'     => $b->status,
                    'user_id'    => $b->user_id,
                    'purpose'    => $b->purpose,
                ],
            ];
        });

        // ---- Field Closures (background events) ----
        $closures = FieldClosure::query()
            ->where('sports_field_id', $field->id)
            ->where(function ($q) use ($start, $end) {
                // overlap rule: (start < end_range) AND (end > start_range)
                $q->where('start_datetime', '<', $end)
                  ->where('end_datetime', '>', $start);
            })
            ->get();

        $closureEvents = $closures->map(function ($c) {
            return [
                'id'           => 'closure-'.$c->id,
                'title'        => $c->reason ? 'Closed: '.$c->reason : 'Closed',
                'start'        => Carbon::parse($c->start_datetime)->toIso8601String(),
                'end'          => Carbon::parse($c->end_datetime)->toIso8601String(),
                // ทำให้เป็น background block (ทับทุกกิจกรรม)
                'display'      => 'background',
                'overlap'      => false,
                'className'    => ['fc-closure'],
                'extendedProps'=> [
                    'created_by' => $c->created_by,
                ],
            ];
        });

        return response()->json($bookingEvents->merge($closureEvents)->values());
    }
}
