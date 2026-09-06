<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SosAlertService;
use Illuminate\Http\Request;

class SosAlertController extends Controller
{
    protected $sosService;

    public function __construct(SosAlertService $sosService)
    {
        $this->sosService = $sosService;
    }

    public function index(Request $request)
    {
        if ($request->query('status') === 'active') {
            $alerts = $this->sosService->getActiveAlerts();
        } else {
            $alerts = $this->sosService->getAllAlerts();
        }
        
        return view('admin.sos.index', compact('alerts'));
    }

    public function show($id)
    {
        $alert = $this->sosService->getAlertById($id);
        return view('admin.sos.show', compact('alert'));
    }

    public function resolve($id)
    {
        $this->sosService->resolveAlert($id);
        return redirect()->back()->with('success', 'Emergency alert marked as resolved.');
    }
}
