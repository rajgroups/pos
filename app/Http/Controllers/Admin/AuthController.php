<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    // Login Page Form
    public function adminLoginForm(){

        // return view login blade design
        return view('admin.authentication.login');
    }

    public function adminLoginSubmit(Request $request){
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (\Illuminate\Support\Facades\Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('admin.home');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    // Verfiy Otp Login Form
    public function adminVerifyOtpForm(){

        // return view form otp verify form
        return view('admin.authentication.otp');
    }

}
