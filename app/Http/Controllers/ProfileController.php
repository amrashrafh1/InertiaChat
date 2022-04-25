<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function index()
    {
        
        return Inertia::render('Profile', ['user' => auth()->user()]);
    }

    public function update(ProfileRequest $request) {

        $user = User::findOrFail(auth()->user()->id);
        $user->update($request->validated());

        return redirect()->route('dashboard')->with('success', 'Profile updated successfully');
    }
}
