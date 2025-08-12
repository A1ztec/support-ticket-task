<?php

namespace App\Http\Controllers\Admin;

use App\Enums\User\UserRole;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Auth\LoginRequest;

class AuthController extends Controller
{
    public function showLogin()
    {

        if (Auth::check() && Auth::user()->role === UserRole::ADMIN) {
            return redirect()->route('admin.dashboard.index');
        }

        return view('admin.auth.login');
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        if (!Auth::attempt($data)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->withInput($request->only('email'));
        }


        if (Auth::user()->role !== UserRole::ADMIN) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'You do not have permission to access the admin panel.',
            ])->withInput($request->only('email'));
        }

        Log::info("Admin logged in successfully", [
            'user_id' => Auth::id(),
            'email' => Auth::user()->email
        ]);

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard.index');
    }

    public function logout(Request $request)
    {
        $userId = Auth::id();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info("Admin logged out successfully", ['user_id' => $userId]);

        return redirect()->route('admin.login');
    }
}
