<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RideController extends Controller
{
    public function active()
    {
        return view('admin.ride.active');
    }

    public function cancelled()
    {
        return view('admin.ride.cancel');
    }

    public function completed()
    {
        return view('admin.ride.complete');
    }

}
