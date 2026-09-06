<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    /**
     * Display a listing of enquiries (Driver Registration Requests).
     */
    public function index()
    {
        $enquiries = Enquiry::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.enquiries.index', compact('enquiries'));
    }

    /**
     * Delete an enquiry after it has been handled.
     */
    public function destroy($id)
    {
        $enquiry = Enquiry::findOrFail($id);
        $enquiry->delete();
        return redirect()->back()->with('success', 'Enquiry successfully deleted.');
    }
}
