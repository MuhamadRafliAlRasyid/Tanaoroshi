<?php

namespace App\Http\Controllers;

use App\Models\Spareparts;
use App\Models\PurchaseRequest;
use App\Models\Pengambilan;


class AdminDashboardController extends Controller
{
    public function index()
    {
        $sparepartCount = Spareparts::count();
        $purchaseRequestCount = PurchaseRequest::count();
        $pengambilanCount = Pengambilan::count();

        return view('admin.dashboard', compact('sparepartCount', 'purchaseRequestCount', 'pengambilanCount'));
    }
}
