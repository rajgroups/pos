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

    // Verfiy Otp Login Form
    public function adminVerifyOtpForm(){

        // return view form otp verify form
        return view('admin.authentication.otp');
    }

}
