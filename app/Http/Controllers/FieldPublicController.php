<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SportsField;
use App\Models\Booking;
use App\Models\FieldClosure;
use Carbon\Carbon;

class FieldPublicController extends Controller
{
    // แสดงหน้ารายละเอียดสนาม + ปฏิทิน
    public function show($fieldId)
    {
        $field = SportsField::findOrFail($fieldId);
        return view('user.fields.show', compact('field'));
    }

    // คืน JSON events ให้ FullCalendar
    // GET /api/fields/{field}/events?start=...&end=...
    public function events(Request $request, $fieldId)
    {
        $field = SportsField::findOrFail($fieldId);

        $start = Carbon::parse($request->query('start'));
        $end   = Carbon::parse($request->query('end'));

        // ดึง booking เฉพาะช่วงวันที่เห็นบนปฏิทิน
        // ฝั่งผู้ใช้ แนะนำให้ซ่อน rejected/cancelled เพื่อให้ตาราง “อ่านง่าย”
        $bookings = Booking::query()
            ->where('sports_field_id', $field->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->whereNotIn('status', ['rejected','cancelled'])
            ->get();

        $bookingEvents = $bookings->map(function ($b) {
            $date = \Carbon\Carbon::parse($b->date)->toDateString(); // YYYY-MM-DD

            // ดึงเฉพาะเวลา (กรณี $b->s
            // tart_time เป็น Carbon ให้ format ออกมา)
            $startTime = $b->start_time instanceof \Carbon\Carbon
                ? $b->start_time->format('H:i:s')
                : (string) $b->start_time;

            $endTime = $b->end_time instanceof \Carbon\Carbon
                ? $b->end_time->format('H:i:s')
                : (string) $b->end_time;

            $startDT = \Carbon\Carbon::parse("$date $startTime")->toIso8601String();
            $endDT   = \Carbon\Carbon::parse("$date $endTime")->toIso8601String();

            $class = match ($b->status) {
                'approved'  => 'fc-booking-approved',
                'pending'   => 'fc-booking-pending',
                'completed' => 'fc-booking-completed',
                default     => 'fc-booking-default',
            };

            return [
                'id'        => 'booking-'.$b->id,
                'title'     => $b->status === 'pending' ? 'Pending booking' : 'Booked',
                'start'     => $startDT,
                'end'       => $endDT,
                'className' => [$class],
            ];
        });


        // ปิดสนาม (แสดงเป็น background block)
        $closures = FieldClosure::query()
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
                'start'     => Carbon::parse($c->start_datetime)->toIso8601String(),
                'end'       => Carbon::parse($c->end_datetime)->toIso8601String(),
                'display'   => 'background',
                'overlap'   => false,
                'className' => ['fc-closure'],
            ];
        });

        return response()->json($bookingEvents->merge($closureEvents)->values());
    }
}
