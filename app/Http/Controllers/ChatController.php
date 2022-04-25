<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Models\Message;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function index()
    {
        $rooms = auth()->user()->rooms;
        $roomArrays = $rooms->map(function($value) {
            return [
                'room_id' => $value->channel_id,
                'user' => $value->users->filter(function($user) {
                    return $user->id != auth()->user()->id;
                })->map(function($value) {
                    return [
                        'user_id'   => $value->id,
                        'user_name' => $value->name,
                        'avatar'    => $value->avatar,
                    ];
                }),
                'message' => $value->messages()->latest()->pluck('message')->first(),
            ];
        })->toArray();

        return Inertia::render('Chat', ['rooms' => $roomArrays]);
    }

    public function get_messages(Request $request, $room_id)
    {
        if ($request->ajax()) {
            $room = Room::where('channel_id',$room_id)->first();
            $messages = [];
            if ($room) {
                $messages = $room->messages()->get()->map(function($message) {
                    return [
                        'type' => $message->type,
                        'user' => $message->sender_id,
                        'message' => $message->message,
                        'time' => Carbon::create($message->created_at)->diffForHumans(),
                    ];
                })->toArray();
            }
            return response()->json($messages);
        };
    }

    public function send_message(Request $request, $room_id)
    {
        $validate = $request->validate([
            'message' => 'required|string',
        ]);
        $room = Room::where('channel_id',$room_id)->first();
        if($room) {
            // send message
            $message = auth()->user()->messages()->create([
                'message' => $validate['message'],
                'room_id' => $room->id,
            ]);
            broadcast(new MessageEvent($message->message, $room->channel_id))->toOthers();
            return response()->json(['success' => true]);
        }
        else {
            return response()->json(['success' => false]);
        }
    }
}
