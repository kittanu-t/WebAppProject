<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

use App\Models\User;
use App\Models\SportsField;
use App\Models\FieldUnit;
use App\Models\Booking;
use App\Models\FieldClosure;
use App\Models\Announcement;
use App\Models\BookingLog;

class InitialTestDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // ล้างข้อมูลเก่า (เฉพาะตารางหลักที่ใช้ทดสอบ)
        DB::table('booking_logs')->truncate();
        DB::table('bookings')->truncate();
        DB::table('field_closures')->truncate();
        DB::table('field_units')->truncate();
        DB::table('sports_fields')->truncate();
        DB::table('announcements')->truncate();
        DB::table('notifications')->truncate();

        // หมายเหตุ: ถ้าจะล้าง users ด้วย ให้ uncomment บรรทัดต่อไปนี้
        // DB::table('users')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ===== USERS =====
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password'),
                'role'     => 'admin',
                'phone'    => '080-000-0000',
                'active'   => 1,
            ]
        );

        $staff1 = User::firstOrCreate(
            ['email' => 'staff1@example.com'],
            [
                'name'     => 'Staff One',
                'password' => Hash::make('password'),
                'role'     => 'staff',
                'phone'    => '080-111-1111',
                'active'   => 1,
            ]
        );

        $staff2 = User::firstOrCreate(
            ['email' => 'staff2@example.com'],
            [
                'name'     => 'Staff Two',
                'password' => Hash::make('password'),
                'role'     => 'staff',
                'phone'    => '080-222-2222',
                'active'   => 1,
            ]
        );

        // ผู้ใช้ทั่วไป 2 คน
        $user1 = User::firstOrCreate(
            ['email' => 'user1@example.com'],
            [
                'name'     => 'Alice User',
                'password' => Hash::make('password'),
                'role'     => 'user',
                'phone'    => '081-111-1111',
                'active'   => 1,
            ]
        );
        $user2 = User::firstOrCreate(
            ['email' => 'user2@example.com'],
            [
                'name'     => 'Bob User',
                'password' => Hash::make('password'),
                'role'     => 'user',
                'phone'    => '082-222-2222',
                'active'   => 1,
            ]
        );

        // ===== SPORTS FIELDS & UNITS =====
        $fieldsData = [
            [
                'name' => 'Main Court A',
                'sport_type' => 'Badminton',
                'location' => 'Building A',
                'capacity' => 20,
                'status' => 'available',
                'owner_id' => $staff1->id,
                'min_duration_minutes' => 60,
                'max_duration_minutes' => 180,
                'lead_time_hours' => 1,
                'units' => 4, // Court 1..4
            ],
            [
                'name' => 'Stadium B',
                'sport_type' => 'Futsal',
                'location' => 'Building B',
                'capacity' => 10,
                'status' => 'available',
                'owner_id' => $staff2->id,
                'min_duration_minutes' => 60,
                'max_duration_minutes' => 180,
                'lead_time_hours' => 2,
                'units' => 2, // Pitch 1..2
            ],
        ];

        $fields = [];
        foreach ($fieldsData as $fd) {
            $field = SportsField::create([
                'name' => $fd['name'],
                'sport_type' => $fd['sport_type'],
                'location' => $fd['location'],
                'capacity' => $fd['capacity'],
                'status' => $fd['status'],
                'owner_id' => $fd['owner_id'],
                'min_duration_minutes' => $fd['min_duration_minutes'],
                'max_duration_minutes' => $fd['max_duration_minutes'],
                'lead_time_hours' => $fd['lead_time_hours'],
            ]);
            $fields[] = $field;

            for ($i=1; $i <= $fd['units']; $i++) {
                FieldUnit::create([
                    'sports_field_id' => $field->id,
                    'name'   => $fd['sport_type'] === 'Badminton' ? "Court {$i}" : "Pitch {$i}",
                    'index'  => $i,
                    'status' => 'available',
                    'capacity' => 1,
                ]);
            }
        }

        // Reload units relationship
        $fields = SportsField::with('units')->get();
        $now    = Carbon::now();
        $today  = $now->copy()->toDateString();
        $tomorrow = $now->copy()->addDay()->toDateString();

        // ===== BOOKINGS (ไม่ชนกันภายในคอร์ตเดียวกัน) =====
        // เราจะสร้างจอง 6 รายการ:
        // - 3 อนุมัติเรียบร้อย, 1 pending, 1 rejected, 1 cancelled
        $makeBooking = function(User $u, SportsField $f, FieldUnit $unit, string $date, string $start, string $end, string $status, ?User $approver=null) {
            $b = Booking::create([
                'user_id' => $u->id,
                'sports_field_id' => $f->id,
                'field_unit_id'   => $unit->id,
                'date'       => $date,
                'start_time' => $start,
                'end_time'   => $end,
                'status'     => $status,
                'purpose'    => 'Test booking',
                'contact_phone' => $u->phone,
                'approved_by' => in_array($status, ['approved','rejected','cancelled','completed']) ? ($approver?->id ?? null) : null,
                'approved_at' => in_array($status, ['approved','rejected','cancelled','completed']) ? Carbon::now() : null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Logs
            BookingLog::create([
                'booking_id' => $b->id,
                'action'     => 'created',
                'by_user_id' => $u->id,
                'note'       => null,
                'created_at' => Carbon::now(),
            ]);
            if ($status !== 'pending') {
                BookingLog::create([
                    'booking_id' => $b->id,
                    'action'     => $status,
                    'by_user_id' => $approver?->id ?? $u->id,
                    'note'       => $status === 'rejected' ? 'Test reject' : ($status === 'cancelled' ? 'Test cancel' : null),
                    'created_at' => Carbon::now(),
                ]);
            }

            // Notifications (ง่าย ๆ)
            DB::table('notifications')->insert([
                'user_id' => $u->id,
                'type'    => 'booking.status.changed',
                'data'    => json_encode([
                    'booking_id' => $b->id,
                    'status'     => $status,
                    'message'    => "สถานะการจองของคุณ: {$status}",
                    'field'      => $f->name,
                    'unit'       => $unit->name,
                    'date'       => $date,
                    'time'       => "{$start}-{$end}",
                ], JSON_UNESCAPED_UNICODE),
                'read_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            return $b;
        };

        // เลือก unit บางตัว
        $fieldA = $fields[0];
        $unitA1 = $fieldA->units[0];
        $unitA2 = $fieldA->units[1];

        $fieldB = $fields[1];
        $unitB1 = $fieldB->units[0];

        // สร้าง bookings (วันที่วันนี้/พรุ่งนี้/ +1 สัปดาห์)
        $makeBooking($user1, $fieldA, $unitA1, $today,     '09:00:00', '10:00:00', 'approved', $staff1);
        $makeBooking($user2, $fieldA, $unitA1, $today,     '10:30:00', '11:30:00', 'approved', $staff1); // ไม่ชนกับ 09-10
        $makeBooking($user1, $fieldA, $unitA2, $today,     '09:00:00', '10:00:00', 'pending',  null);    // คนละคอร์ต
        $makeBooking($user2, $fieldB, $unitB1, $tomorrow,  '14:00:00', '15:00:00', 'rejected', $staff2);
        $makeBooking($user1, $fieldB, $unitB1, $tomorrow,  '16:00:00', '17:00:00', 'cancelled',$staff2);
        $makeBooking($user2, $fieldA, $unitA2, Carbon::now()->addWeek()->toDateString(), '18:00:00', '19:00:00', 'approved', $staff1);

        // ===== FIELD CLOSURES =====
        // ปิดทั้งสนาม A วันนี้ช่วงบ่าย
        FieldClosure::create([
            'sports_field_id' => $fieldA->id,
            'field_unit_id'   => null, // ทั้งสนาม
            'start_datetime'  => Carbon::parse($today.' 13:00:00'),
            'end_datetime'    => Carbon::parse($today.' 15:00:00'),
            'reason'          => 'Cleaning',
            'created_by'      => $staff1->id,
            'created_at'      => Carbon::now(),
            'updated_at'      => Carbon::now(),
        ]);

        // ปิดเฉพาะ Court 2 ของสนาม A พรุ่งนี้ทั้งวัน
        FieldClosure::create([
            'sports_field_id' => $fieldA->id,
            'field_unit_id'   => $unitA2->id,
            'start_datetime'  => Carbon::parse($tomorrow.' 08:00:00'),
            'end_datetime'    => Carbon::parse($tomorrow.' 22:00:00'),
            'reason'          => 'Maintenance Court 2',
            'created_by'      => $staff1->id,
            'created_at'      => Carbon::now(),
            'updated_at'      => Carbon::now(),
        ]);

        // ===== ANNOUNCEMENTS =====
        Announcement::create([
            'title'        => 'ยินดีต้อนรับสู่ระบบจองสนาม',
            'content'      => 'ประกาศทดสอบสำหรับผู้ใช้ทุกท่าน',
            'audience'     => 'all',
            'created_by'   => $admin->id,
            'published_at' => Carbon::now()->subDay(),
        ]);
        Announcement::create([
            'title'        => "สนาม {$fieldA->name} ปิดบางช่วงวันนี้",
            'content'      => 'โปรดตรวจสอบปฏิทินก่อนทำการจอง',
            'audience'     => 'users',
            'created_by'   => $staff1->id,
            'published_at' => Carbon::now(),
        ]);

        // DONE
    }
}
