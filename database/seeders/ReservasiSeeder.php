<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Reservasi;

class ReservasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Reservasi::create([
            'booking_code' => 'BK001',
            'guest_name' => 'Budi',
            'room_type' => 'Deluxe',
            'check_in_date' => '2026-06-10',
            'check_out_date' => '2026-06-12',
            'status' => 'confirmed'
        ]);

        Reservasi::create([
            'booking_code' => 'BK002',
            'guest_name' => 'Andi',
            'room_type' => 'Suite',
            'check_in_date' => '2026-06-11',
            'check_out_date' => '2026-06-15',
            'status' => 'pending'
        ]);

        Reservasi::create([
            'booking_code' => 'BK003',
            'guest_name' => 'Siti',
            'room_type' => 'Standard',
            'check_in_date' => '2026-06-20',
            'check_out_date' => '2026-06-22',
            'status' => 'confirmed'
        ]);

        Reservasi::create([
            'booking_code' => 'BK004',
            'guest_name' => 'Rina',
            'room_type' => 'Executive',
            'check_in_date' => '2026-06-25',
            'check_out_date' => '2026-06-27',
            'status' => 'pending'
        ]);

        Reservasi::create([
            'booking_code' => 'BK005',
            'guest_name' => 'Dedi',
            'room_type' => 'Suite',
            'check_in_date' => '2026-07-01',
            'check_out_date' => '2026-07-05',
            'status' => 'cancelled'
        ]);
    }
}
