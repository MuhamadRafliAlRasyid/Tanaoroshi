<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KaryawanDashboardController extends Controller
{
    public function index()
    {
        return view('karyawan.dashboard');
    }
}
