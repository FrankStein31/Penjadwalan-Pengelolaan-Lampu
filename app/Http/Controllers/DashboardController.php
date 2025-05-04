<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lampu;
use App\Models\Energi;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Hitung jumlah lampu berdasarkan status
        $lampuAktif = Lampu::where('status', 1)->count();
        $lampuNonaktif = Lampu::where('status', 0)->count();
        $totalLampu = Lampu::count();
        
        // Data penggunaan energi per hari (dummy data)
        $hariSeminggu = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $dataEnergi = [12, 19, 3, 5, 2, 7, 9]; // Ini bisa diganti dengan data dari database
        
        return view('dashboard.index', compact('lampuAktif', 'lampuNonaktif', 'totalLampu', 'hariSeminggu', 'dataEnergi'));
    }

    public function lampu()
    {
        return view('dashboard.lampu');
    }

    public function jadwal()
    {
        return view('dashboard.jadwal');
    }
}
