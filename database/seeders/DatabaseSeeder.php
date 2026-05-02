<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        DB::table('units')->insert([
            'name' => 'APT100',
            'id_owner' => '1',
        ]);
        DB::table('units')->insert([
            'name' => 'APT101',
            'id_owner' => '1',
        ]);
        DB::table('units')->insert([
            'name' => 'APT200',
            'id_owner' => '0',
        ]);
        DB::table('units')->insert([
            'name' => 'APT201',
            'id_owner' => '0',
        ]);
        DB::table('areas')->insert([
            'allowed' => '1',
            'title' => 'Gym',
            'cover' => 'gym.jpg',
            'days' => '1,3,5,6',
            'start_time' => '06:00:00',
            'end_time' => '22:00:00'
        ]);
        DB::table('areas')->insert([
            'allowed' => '1',
            'title' => 'Pool',
            'cover' => 'poll.jpg',
            'days' => '1,2,3,4,5,6',
            'start_time' => '08:00:00',
            'end_time' => '20:00:00'
        ]);
        DB::table('areas')->insert([
            'allowed' => '1',
            'title' => 'Grill',
            'cover' => 'Grill.jpg',
            'days' => '4,5,6',
            'start_time' => '18:00:00',
            'end_time' => '00:00:00'
        ]);
        DB::table('walls')->insert([
           'title'=>'TEST NOTICE',
           'body'=>'TEST BODY',
           'date_created'=>'2026-02-05 00:35:00'
        ]);
        DB::table('walls')->insert([
           'title'=>'TEST NOTICE 2',
           'body'=>'TEST BODY 2',
           'date_created'=>'2026-02-05 01:35:00'
        ]);
    }
}
