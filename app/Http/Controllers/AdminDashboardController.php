<?php

namespace App\Http\Controllers;

use App\Models\Spareparts;
use App\Models\PurchaseRequest;
use App\Models\PengambilanSparepart;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $sparepartCount = Spareparts::count();
        $purchaseRequestCount = PurchaseRequest::count();
        $pengambilanCount = PengambilanSparepart::count();

        return view('admin.dashboard', compact('sparepartCount', 'purchaseRequestCount', 'pengambilanCount'));
    }
}
