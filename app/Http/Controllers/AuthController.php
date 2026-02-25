<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Credentials sederhana (bisa diganti dengan database)
        if ($request->username === 'admin' && $request->password === 'admin123') {
            session(['admin_logged_in' => true]);
            return redirect()->route('admin.readings.gabungan')
                ->with('success', 'Login berhasil!');
        }

        return back()->withErrors([
            'login' => 'Username atau password salah.',
        ]);
    }

    public function logout()
    {
        session()->forget('admin_logged_in');
        return redirect()->route('home')
            ->with('success', 'Logout berhasil!');
    }
}