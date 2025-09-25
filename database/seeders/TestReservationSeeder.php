<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reservation;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;

class TestReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first unit and user for testing
        $unit = Unit::first();
        $user = User::first();

        if (!$unit || !$user) {
            $this->command->error('No units or users found. Please run UnitSeeder and UserSeeder first.');
            return;
        }

        $this->command->info('Creating test reservations...');

        // Test Reservation 1: Pending with deposit
        Reservation::create([
            'user_id' => $user->id,
            'unit_id' => $unit->id,
            'reservation_number' => 'RES-' . time() . '-001',
            'check_in_date' => Carbon::now()->addDays(5),
            'check_out_date' => Carbon::now()->addDays(8),
            'guest_name' => 'John Doe',
            'guest_phone' => '+1234567890',
            'guest_email' => 'john.doe@example.com',
            'number_of_guests' => 2,
            'special_requests' => 'Early check-in if possible',
            'total_amount' => 12000,
            'status' => 'pending',
            'transfer_amount' => 6000,
            'transfer_date' => Carbon::now()->subDays(2),
            'transfer_image' => 'transfers/test_transfer_1.jpg',
            'deposit_amount' => 3000,
            'deposit_image' => 'deposits/test_deposit_1.jpg',
            'admin_notes' => 'Guest requested early check-in. Verify deposit receipt.',
            'minimum_deposit_amount' => 5000,
            'deposit_percentage' => 50,
        ]);

        // Test Reservation 2: Confirmed
        Reservation::create([
            'user_id' => $user->id,
            'unit_id' => $unit->id,
            'reservation_number' => 'RES-' . time() . '-002',
            'check_in_date' => Carbon::now()->addDays(15),
            'check_out_date' => Carbon::now()->addDays(18),
            'guest_name' => 'Jane Smith',
            'guest_phone' => '+1987654321',
            'guest_email' => 'jane.smith@example.com',
            'number_of_guests' => 3,
            'special_requests' => 'Late check-out preferred',
            'total_amount' => 15000,
            'status' => 'confirmed',
            'transfer_amount' => 7500,
            'transfer_date' => Carbon::now()->subDays(5),
            'transfer_image' => 'transfers/test_transfer_2.jpg',
            'deposit_amount' => 7500,
            'deposit_image' => 'deposits/test_deposit_2.jpg',
            'admin_notes' => 'Deposit verified. Guest is a returning customer.',
            'minimum_deposit_amount' => 0,
            'deposit_percentage' => 50,
        ]);

        // Test Reservation 3: Active (current stay)
        Reservation::create([
            'user_id' => $user->id,
            'unit_id' => $unit->id,
            'reservation_number' => 'RES-' . time() . '-003',
            'check_in_date' => Carbon::now()->subDays(2),
            'check_out_date' => Carbon::now()->addDays(3),
            'guest_name' => 'Mike Johnson',
            'guest_phone' => '+1555123456',
            'guest_email' => 'mike.johnson@example.com',
            'number_of_guests' => 4,
            'special_requests' => 'Extra towels needed',
            'total_amount' => 18000,
            'status' => 'active',
            'transfer_amount' => 9000,
            'transfer_date' => Carbon::now()->subDays(10),
            'transfer_image' => 'transfers/test_transfer_3.jpg',
            'deposit_amount' => 9000,
            'deposit_image' => 'deposits/test_deposit_3.jpg',
            'admin_notes' => 'Guest is currently staying. All payments completed.',
            'minimum_deposit_amount' => 0,
            'deposit_percentage' => 50,
        ]);

        // Test Reservation 4: Future confirmed
        Reservation::create([
            'user_id' => $user->id,
            'unit_id' => $unit->id,
            'reservation_number' => 'RES-' . time() . '-004',
            'check_in_date' => Carbon::now()->addDays(30),
            'check_out_date' => Carbon::now()->addDays(35),
            'guest_name' => 'Sarah Wilson',
            'guest_phone' => '+1444333222',
            'guest_email' => 'sarah.wilson@example.com',
            'number_of_guests' => 2,
            'special_requests' => 'Quiet room preferred',
            'total_amount' => 20000,
            'status' => 'confirmed',
            'transfer_amount' => 10000,
            'transfer_date' => Carbon::now()->subDays(1),
            'transfer_image' => 'transfers/test_transfer_4.jpg',
            'deposit_amount' => 10000,
            'deposit_image' => 'deposits/test_deposit_4.jpg',
            'admin_notes' => 'Deposit received. Guest prefers quiet location.',
            'minimum_deposit_amount' => 8000,
            'deposit_percentage' => 50,
        ]);

        // Test Reservation 5: Pending without deposit
        Reservation::create([
            'user_id' => $user->id,
            'unit_id' => $unit->id,
            'reservation_number' => 'RES-' . time() . '-005',
            'check_in_date' => Carbon::now()->addDays(45),
            'check_out_date' => Carbon::now()->addDays(48),
            'guest_name' => 'Alex Brown',
            'guest_phone' => '+1777888999',
            'guest_email' => 'alex.brown@example.com',
            'number_of_guests' => 1,
            'special_requests' => 'Ground floor room if available',
            'total_amount' => 16000,
            'status' => 'pending',
            'transfer_amount' => null,
            'transfer_date' => null,
            'transfer_image' => null,
            'deposit_amount' => null,
            'deposit_image' => null,
            'admin_notes' => 'Waiting for deposit payment. Guest needs ground floor.',
            'minimum_deposit_amount' => 6000,
            'deposit_percentage' => 40,
        ]);

        // Test Reservation 6: Completed
        Reservation::create([
            'user_id' => $user->id,
            'unit_id' => $unit->id,
            'reservation_number' => 'RES-' . time() . '-006',
            'check_in_date' => Carbon::now()->subDays(20),
            'check_out_date' => Carbon::now()->subDays(15),
            'guest_name' => 'Emily Davis',
            'guest_phone' => '+1666777888',
            'guest_email' => 'emily.davis@example.com',
            'number_of_guests' => 3,
            'special_requests' => 'Late check-out granted',
            'total_amount' => 14000,
            'status' => 'completed',
            'transfer_amount' => 7000,
            'transfer_date' => Carbon::now()->subDays(25),
            'transfer_image' => 'transfers/test_transfer_6.jpg',
            'deposit_amount' => 7000,
            'deposit_image' => 'deposits/test_deposit_6.jpg',
            'admin_notes' => 'Stay completed successfully. Guest was satisfied.',
            'minimum_deposit_amount' => 0,
            'deposit_percentage' => 50,
        ]);

        $this->command->info('✅ 6 test reservations created successfully!');
        $this->command->info('📅 Date ranges covered:');
        $this->command->info('   - Past (completed): ' . Carbon::now()->subDays(20)->format('Y-m-d') . ' to ' . Carbon::now()->subDays(15)->format('Y-m-d'));
        $this->command->info('   - Current (active): ' . Carbon::now()->subDays(2)->format('Y-m-d') . ' to ' . Carbon::now()->addDays(3)->format('Y-m-d'));
        $this->command->info('   - Near future (pending): ' . Carbon::now()->addDays(5)->format('Y-m-d') . ' to ' . Carbon::now()->addDays(8)->format('Y-m-d'));
        $this->command->info('   - Future (confirmed): ' . Carbon::now()->addDays(15)->format('Y-m-d') . ' to ' . Carbon::now()->addDays(35)->format('Y-m-d'));
        $this->command->info('   - Far future (pending): ' . Carbon::now()->addDays(45)->format('Y-m-d') . ' to ' . Carbon::now()->addDays(48)->format('Y-m-d'));
    }
}
