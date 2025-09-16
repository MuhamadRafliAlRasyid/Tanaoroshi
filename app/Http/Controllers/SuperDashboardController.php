<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\RequestLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class SuperDashboardController extends BaseController
{
    public function __construct() {}

    public function index()
    {
        $totalRequests = PurchaseRequest::count();
        $approvedRequests = PurchaseRequest::where('status', 'PO')->count();
        $rejectedRequests = RequestLog::where('action', 'rejected')->distinct('purchase_request_id')->count('purchase_request_id');
        $recentLogs = RequestLog::with(['purchaseRequest', 'approvedBy'])->latest()->take(5)->get();

        return view('super.dashboard', compact('totalRequests', 'approvedRequests', 'rejectedRequests', 'recentLogs'));
    }
}
