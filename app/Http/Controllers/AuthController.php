<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();

        User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('login')->with('success', 'Đăng ký thành công. Hãy đăng nhập.');
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials)) {
            // Lỗi chung, không tiết lộ email có tồn tại hay không.
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['login' => 'Email hoặc mật khẩu không đúng.']);
        }

        // [BỔ SUNG] Tạo lại session ID sau khi đăng nhập để chống session fixation.
        $request->session()->regenerate();

        return redirect()->intended(route('tasks.index'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        // [BỔ SUNG] Hủy session và tạo lại CSRF token khi đăng xuất.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Bạn đã đăng xuất.');
    }
}
