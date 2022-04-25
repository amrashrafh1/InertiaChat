<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $room = Room::create([
            'channel_id' => rand(1, 100) . '-' . time(),
        ]);
        $room->users()->attach([1, 2]);
        $room->messages()->create(
            [
                'type' => 'text',
                'sender_id' => 1,
                'message' => 'Hey there. We would like to invite you over to our office for a visit. How about it?',
            ],
        );
        $room->messages()->create(
            [
                'type' => 'text',
                'sender_id' => 2,
                'message' => 'All travel expenses are covered by us of course :D',
            ],
        );
        $room->messages()->create(
            [
                'type' => 'text',
                'sender_id' => 1,
                'message' => 'I accept. Thank you very much.',
            ]
        );
    }
}
