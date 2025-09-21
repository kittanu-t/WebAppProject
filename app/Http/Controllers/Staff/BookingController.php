<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use App\Models\BookingLog;
use App\Models\UserNotification;

class BookingController extends Controller
{
    /**
     * แสดงรายการ booking ที่ pending ของสนามที่ staff เป็นเจ้าของ
     */
    public function index(Request $request)
    {
        $staff = $request->user();

        $bookings = Booking::with(['sportsField','user'])
            ->whereHas('sportsField', function ($q) use ($staff) {
                $q->where('owner_id', $staff->id);
            })
            ->where('status', 'pending')
            ->orderBy('date')
            ->orderBy('start_time')
            ->paginate(15);

        return view('staff.bookings.index', compact('bookings'));
    }

    /**
     * อนุมัติ booking
     */
    public function approve(Request $request, $id)
    {
        $staff = $request->user();

        DB::transaction(function () use ($id, $staff) {
            $booking = Booking::lockForUpdate()->findOrFail($id);

            if ($booking->sportsField->owner_id !== $staff->id) {
                abort(403, 'คุณไม่มีสิทธิ์จัดการสนามนี้');
            }

            $booking->status = 'approved';
            $booking->approved_by = $staff->id;
            $booking->approved_at = now();
            $booking->save();

            BookingLog::create([
                'booking_id' => $booking->id,
                'action'     => 'approved',
                'by_user_id' => $staff->id,
                'created_at' => now(),
            ]);

            UserNotification::create([
                'user_id' => $booking->user_id,
                'type'    => 'booking.status.changed',
                'data'    => [
                    'booking_id' => $booking->id,
                    'status'     => 'approved',
                    'message'    => 'การจองของคุณได้รับการอนุมัติแล้ว',
                ],
            ]);
        });

        return back()->with('status','อนุมัติเรียบร้อย');
    }

    /**
     * ปฏิเสธ booking
     */
    public function reject(Request $request, $id)
    {
        $staff = $request->user();

        DB::transaction(function () use ($id, $staff) {
            $booking = Booking::lockForUpdate()->findOrFail($id);

            if ($booking->sportsField->owner_id !== $staff->id) {
                abort(403, 'คุณไม่มีสิทธิ์จัดการสนามนี้');
            }

            $booking->status = 'rejected';
            $booking->approved_by = $staff->id;
            $booking->approved_at = now();
            $booking->save();

            BookingLog::create([
                'booking_id' => $booking->id,
                'action'     => 'rejected',
                'by_user_id' => $staff->id,
                'created_at' => now(),
            ]);

            UserNotification::create([
                'user_id' => $booking->user_id,
                'type'    => 'booking.status.changed',
                'data'    => [
                    'booking_id' => $booking->id,
                    'status'     => 'rejected',
                    'message'    => 'คำขอจองของคุณถูกปฏิเสธ',
                ],
            ]);
        });

        return back()->with('status','ปฏิเสธเรียบร้อย');
    }
}
