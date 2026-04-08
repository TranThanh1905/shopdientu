<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Hiển thị form đăng ký
     */
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.required'      => 'Vui lòng nhập họ tên',
            'email.required'     => 'Vui lòng nhập email',
            'email.unique'       => 'Email đã được sử dụng',
            'password.required'  => 'Vui lòng nhập mật khẩu',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
        ]);

        // Tạo user
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Trigger event (email verify nếu có)
        event(new Registered($user));

        // Auto login
        Auth::login($user);

        // Redirect sang trang confirm trung gian
        return redirect()->route('register.confirm')
            ->with('success', 'Đăng ký thành công!');
    }

    public function showConfirm(): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return view('auth.register-confirm');
    }

    public function finishConfirm(): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return redirect()->route('home')
            ->with('success', 'Chào mừng ' . Auth::user()->name . ' đến với ElectroShop!');
    }
}