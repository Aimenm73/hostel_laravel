<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Room;
use App\Models\StudentDetail;
use App\Models\Event;
use App\Models\Announcement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        $admin = User::create([
            'name' => 'Admin COMSATS',
            'email' => 'admin@comsats.edu',
            'password' => Hash::make('123456'),
            'role' => 'admin',
        ]);

        $rufah = User::create([
            'name' => 'Rufah Gilani',
            'email' => 'rufah@student.com',
            'password' => Hash::make('123456'),
            'role' => 'student',
        ]);

        $ayesha = User::create([
            'name' => 'Ayesha Ahmed',
            'email' => 'ayesha@student.com',
            'password' => Hash::make('123456'),
            'role' => 'student',
        ]);

        // Rooms
        $room101 = Room::create(['number' => '101', 'floor' => 1, 'type' => 'double', 'capacity' => 2]);
        $room102 = Room::create(['number' => '102', 'floor' => 1, 'type' => 'double', 'capacity' => 2]);
        Room::create(['number' => '103', 'floor' => 1, 'type' => 'single', 'capacity' => 1]);
        Room::create(['number' => '201', 'floor' => 2, 'type' => 'triple', 'capacity' => 3]);
        Room::create(['number' => '202', 'floor' => 2, 'type' => 'suite', 'capacity' => 4]);

        // Student Details
        StudentDetail::create([
            'user_id' => $rufah->id,
            'room_id' => $room101->id,
            'roll_no' => 'SP24-BCS-062',
            'department' => 'Computer Science',
            'year' => 2,
        ]);

        StudentDetail::create([
            'user_id' => $ayesha->id,
            'room_id' => $room102->id,
            'roll_no' => 'SP24-BCS-107',
            'department' => 'Computer Science',
            'year' => 2,
        ]);

        // Events
        Event::create([
            'title' => 'Welcome Party',
            'description' => 'Welcome to new students',
            'venue' => 'Main Auditorium',
            'date' => Carbon::now()->addDays(7)->format('Y-m-d'),
            'time' => '18:00',
            'max_seats' => 100,
            'status' => 'upcoming',
        ]);

        Event::create([
            'title' => 'Tech Workshop',
            'description' => 'Web Development Workshop',
            'venue' => 'CS Lab',
            'date' => Carbon::now()->addDays(14)->format('Y-m-d'),
            'time' => '14:00',
            'max_seats' => 50,
            'status' => 'upcoming',
        ]);

        // Announcements
        Announcement::create([
            'title' => 'Hostel Timings',
            'content' => 'Hostel gates close at 11 PM sharp',
            'type' => 'general',
        ]);

        Announcement::create([
            'title' => 'Exam Schedule',
            'content' => 'Final exams start from next month',
            'type' => 'urgent',
        ]);
    }
}
