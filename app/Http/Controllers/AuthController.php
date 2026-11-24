<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showSignup()
    {
        return view('auth.signup');
    }

    public function signup(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'min:8', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'preferred_lang' => ['nullable', 'in:bn,jp'],
        ]);

        User::create([
            'name' => $validated['name'],
            'phone' => trim($validated['phone']),
            'password' => Hash::make($validated['password']),
            'preferred_lang' => $validated['preferred_lang'] ?? 'bn',
        ]);

        return redirect()->route('login')->with('success', 'অ্যাকাউন্ট সফলভাবে তৈরি হয়েছে! এখন লগইন করুন।');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'phone' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $credentials['phone'] = trim($credentials['phone']);

        if (Auth::attempt(['phone' => $credentials['phone'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'phone' => 'মোবাইল নম্বর বা পাসওয়ার্ড সঠিক নয়।',
        ])->onlyInput('phone');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}