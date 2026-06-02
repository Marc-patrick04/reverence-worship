<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user exists
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if ($user) {
                // User exists, log them in
                Auth::login($user);
                return redirect()->route('admin.dashboard')->with('success', 'Welcome back!');
            } else {
                // Create new user
                $newUser = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(24)),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);
                
                // Assign default role (you can change this)
                $defaultRole = \App\Models\User\Role::where('name', 'admin')->first();
                if ($defaultRole) {
                    $newUser->roles()->attach($defaultRole->id);
                }
                
                Auth::login($newUser);
                return redirect()->route('admin.dashboard')->with('success', 'Account created successfully!');
            }
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Something went wrong! ' . $e->getMessage());
        }
    }
}