<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MemberController extends Controller
{
    public function index()
    {
        $members = User::all()->map(function ($user) {
            return [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'isFriend' => auth()->user()->isFriendWith($user),
            ];
        });
        return Inertia::render('Members', [
            'members' => $members,
        ]);
    }

    public function add_friend(Request $request)
    {
        $validate = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        $user = auth()->user();
        $friend = User::where('email', $validate['email'])->first();

        // check if the user is already a friend
        if ($user->isFriendWith($friend)) {
            // remove the friend
            $user->friends()->detach($friend);
        } else {
            $user->friends()->attach($friend);

            if(!$user->hasRoomWith($friend)) {
                $room = Room::create([
                    'channel_id' => strtotime(auth()->user()->created_at ? auth()->user()->created_at : now()) . '' . time(),
                ]);
                $user->rooms()->attach($room->id);
                $friend->rooms()->attach($room->id);
            }
        }
        return response()->json(['success' => true]);
    }
}
