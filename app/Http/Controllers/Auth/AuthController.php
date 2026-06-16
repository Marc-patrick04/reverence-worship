<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User\User;
use App\Models\System\ActivityLog;
use App\Models\System\SystemSetting;
class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    // First check if user exists
    $user = User::where('email', $request->email)->first();
    
    if ($user && !$user->is_active) {
        if ($user->created_by === null && $user->email_verified_at === null) {
            // This is a pending registration
            return back()->withErrors([
                'email' => 'Your account is pending approval. Please wait for an administrator to approve your account.',
            ])->onlyInput('email');
        }
        // Account is deactivated
        return back()->withErrors([
            'email' => 'Your account has been deactivated. Please contact an administrator.',
        ])->onlyInput('email');
    }

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        
        if (Auth::user()->isSuperAdmin()) {
            return redirect()->intended('/super-admin/dashboard');
        }
        return redirect()->intended('/admin/dashboard');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
}

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
    
    public function showRegister()
    {
        return view('auth.register');
    }
    
    public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6|confirmed',
        'phone' => 'nullable|string|max:20',
        'gender' => 'nullable|string|max:20',
        'date_of_birth' => 'nullable|date',
        'province' => 'nullable|string|max:100',
        'district' => 'nullable|string|max:100',
        'sector' => 'nullable|string|max:100',
        'village' => 'nullable|string|max:100',
        'marital_status' => 'nullable|string|max:50'
    ]);
    
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'is_active' => false,
        'email_verified_at' => null,
        'created_by' => null,
        'phone' => $request->phone,
        'gender' => $request->gender,
        'date_of_birth' => $request->date_of_birth,
        'province' => $request->province,
        'district' => $request->district,
        'sector' => $request->sector,
        'village' => $request->village,
        'marital_status' => $request->marital_status
    ]);
    
    return redirect()->route('login')->with('warning', 
        'Your account has been created and is pending approval. You will be notified once an administrator approves your account.');
}
}