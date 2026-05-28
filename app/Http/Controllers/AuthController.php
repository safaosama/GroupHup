<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'password'   => 'required',
        ]);

        $credentials = $request->only('student_id', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = auth()->user();
            if ($user->role == 'instructor') {
                return redirect('/instructor');
            }

            return redirect('/student');
        }

        return back()->with('error', 'Invalid ID or Password');
    }

    public function showRegister()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'student_id' => 'required|unique:users,student_id',
            'password'   => 'required|min:6',
            'role'       => 'required|in:student,instructor',
        ]);

        $user = User::create([
            'name'       => $request->name,
            'student_id' => $request->student_id,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
        ]);

        Auth::login($user);

        if ($user->role === 'instructor') {
            return redirect('/instructor');
        }

        return redirect('/student');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
