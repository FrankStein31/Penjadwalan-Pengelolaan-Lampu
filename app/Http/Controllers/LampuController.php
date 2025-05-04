<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lampu;
use App\Models\Energi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LampuController extends Controller
{
    /**
     * Tampilkan daftar lampu.
     */
    public function index()
    {
        $lampu = Lampu::all();
        return view('dashboard.lampu', compact('lampu'));
    }

    /**
     * Tampilkan form tambah lampu.
     */
    public function create()
    {
        return view('dashboard.lampu-create');
    }

    /**
     * Simpan lampu baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lampu' => 'required|string|max:255',
            'lokasi' => 'required|string|max:100',
        ]);

        Lampu::create([
            'nama_lampu' => $request->nama_lampu,
            'lokasi' => $request->lokasi,
            'status' => 0,
            'intensitas' => 0,
        ]);

        return redirect()->route('lampu.index')->with('success', 'Lampu berhasil ditambahkan!');
    }

    /**
     * Tampilkan form edit lampu.
     */
    public function edit(Lampu $lampu)
    {
        return view('dashboard.lampu-edit', compact('lampu'));
    }

    /**
     * Update status dan intensitas lampu.
     */
    public function updateStatus(Request $request, $id)
    {
        $lampu = Lampu::findOrFail($id);
        
        $request->validate([
            'status' => 'required|boolean',
            'intensitas' => 'required|integer|min:0|max:100',
        ]);

        $lampu->update([
            'status' => $request->status,
            'intensitas' => $request->intensitas,
        ]);
        
        // Catat penggunaan energi
        $this->catatPenggunaanEnergi($lampu);

        return response()->json(['success' => true]);
    }
    
    /**
     * Catat penggunaan energi saat status atau intensitas berubah
     */
    private function catatPenggunaanEnergi($lampu)
    {
        // Rumus sederhana untuk menghitung penggunaan energi
        // Intensitas 100% = 10 watt, 0% = 0 watt, dll.
        $energi = ($lampu->status) ? ($lampu->intensitas / 10) : 0;
        
        // Tentukan kondisi berdasarkan intensitas
        $kondisi = 0; // Default: mati
        if ($lampu->status) {
            if ($lampu->intensitas <= 30) {
                $kondisi = 1; // Redup
            } else if ($lampu->intensitas <= 70) {
                $kondisi = 2; // Sedang
            } else {
                $kondisi = 3; // Terang
            }
        }
        
        // Ambil tanggal saat ini
        $now = Carbon::now();
        
        // Catat penggunaan energi ke database
        Energi::create([
            'lampu_id' => $lampu->id,
            'energi' => $energi,
            'kondisi' => $kondisi,
            'durasi' => 1, // Default durasi 1 menit
            'week' => $now->weekOfYear,
            'month' => $now->month,
            'year' => $now->year
        ]);
    }
    
    /**
     * Ambil statistik penggunaan lampu
     */
    public function getStatistik($id)
    {
        $lampu = Lampu::findOrFail($id);
        $now = Carbon::now();
        
        // Ambil data 7 hari terakhir
        $startDate = $now->copy()->subDays(6)->startOfDay();
        $endDate = $now->copy()->endOfDay();
        
        // Ambil data energi
        $energiData = Energi::where('lampu_id', $id)
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->orderBy('created_at')
            ->get();
        
        // Format data untuk grafik penggunaan daya
        $penggunaanData = $this->formatPenggunaanData($energiData, $startDate, $endDate);
        
        // Format data untuk grafik kondisi
        $kondisiData = $this->formatKondisiData($energiData, $startDate, $endDate);
        
        return response()->json([
            'penggunaan' => $penggunaanData,
            'kondisi' => $kondisiData
        ]);
    }
    
    /**
     * Format data penggunaan energi untuk grafik
     */
    private function formatPenggunaanData($energiData, $startDate, $endDate)
    {
        $labels = [];
        $data = [];
        
        // Generate data untuk 7 hari terakhir
        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dayName = $this->getDayName($date->dayOfWeek);
            $labels[] = $dayName;
            
            // Hitung total penggunaan energi per hari
            $dayEnergi = $energiData->filter(function ($item) use ($date) {
                $itemDate = Carbon::parse($item->created_at);
                return $itemDate->isSameDay($date);
            })->sum(function($item) {
                // Energi * durasi = konsumsi daya
                return $item->energi * $item->durasi;
            });
            
            $data[] = round($dayEnergi, 2);
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
    
    /**
     * Format data kondisi lampu untuk grafik
     */
    private function formatKondisiData($energiData, $startDate, $endDate)
    {
        $labels = [];
        $mati = [];
        $redup = [];
        $sedang = [];
        $terang = [];
        
        // Generate data untuk 7 hari terakhir
        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dayName = $this->getDayName($date->dayOfWeek);
            $labels[] = $dayName;
            
            // Filter data untuk hari ini
            $dayData = $energiData->filter(function ($item) use ($date) {
                $itemDate = Carbon::parse($item->created_at);
                return $itemDate->isSameDay($date);
            });
            
            // Menghitung total durasi untuk masing-masing kondisi
            $matiDurasi = $dayData->where('kondisi', 0)->sum('durasi');
            $redupDurasi = $dayData->where('kondisi', 1)->sum('durasi');
            $sedangDurasi = $dayData->where('kondisi', 2)->sum('durasi');
            $terangDurasi = $dayData->where('kondisi', 3)->sum('durasi');
            
            // Tambahkan ke array
            $mati[] = $matiDurasi;
            $redup[] = $redupDurasi;
            $sedang[] = $sedangDurasi;
            $terang[] = $terangDurasi;
        }
        
        return [
            'labels' => $labels,
            'data' => [
                'mati' => $mati,
                'redup' => $redup,
                'sedang' => $sedang,
                'terang' => $terang
            ]
        ];
    }
    
    /**
     * Mendapatkan total penggunaan daya untuk semua lampu
     */
    public function getTotalPenggunaan()
    {
        $now = Carbon::now();
        
        // Data Harian - 7 hari terakhir
        $dailyData = DB::table('energi')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(energi * durasi) as total_energi'))
            ->where('created_at', '>=', $now->copy()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Data Mingguan - 4 minggu terakhir
        $weeklyData = DB::table('energi')
            ->select('year', 'week', DB::raw('SUM(energi * durasi) as total_energi'))
            ->where('created_at', '>=', $now->copy()->subWeeks(4)->startOfDay())
            ->groupBy('year', 'week')
            ->orderBy('year')
            ->orderBy('week')
            ->get();
        
        // Data Bulanan - 6 bulan terakhir
        $monthlyData = DB::table('energi')
            ->select('year', 'month', DB::raw('SUM(energi * durasi) as total_energi'))
            ->where('created_at', '>=', $now->copy()->subMonths(6)->startOfDay())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        
        // Format data untuk direturn
        $dailyLabels = [];
        $dailyValues = [];
        
        foreach ($dailyData as $data) {
            $date = Carbon::parse($data->date);
            $dailyLabels[] = $this->getDayName($date->dayOfWeek);
            $dailyValues[] = round($data->total_energi, 2);
        }
        
        // Format mingguan
        $weeklyLabels = [];
        $weeklyValues = [];
        
        foreach ($weeklyData as $data) {
            $weeklyLabels[] = "Minggu {$data->week}";
            $weeklyValues[] = round($data->total_energi, 2);
        }
        
        // Format bulanan
        $monthlyLabels = [];
        $monthlyValues = [];
        
        foreach ($monthlyData as $data) {
            $monthName = $this->getMonthName($data->month);
            $monthlyLabels[] = $monthName;
            $monthlyValues[] = round($data->total_energi, 2);
        }
        
        return response()->json([
            'daily' => [
                'labels' => $dailyLabels,
                'data' => $dailyValues
            ],
            'weekly' => [
                'labels' => $weeklyLabels,
                'data' => $weeklyValues
            ],
            'monthly' => [
                'labels' => $monthlyLabels,
                'data' => $monthlyValues
            ]
        ]);
    }
    
    /**
     * Mendapatkan nama hari dalam bahasa Indonesia
     */
    private function getDayName($dayOfWeek)
    {
        $days = [
            0 => 'Min',
            1 => 'Sen',
            2 => 'Sel',
            3 => 'Rab',
            4 => 'Kam',
            5 => 'Jum',
            6 => 'Sab',
        ];
        
        return $days[$dayOfWeek];
    }
    
    /**
     * Mendapatkan nama bulan dalam bahasa Indonesia
     */
    private function getMonthName($month)
    {
        $months = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Ags',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des',
        ];
        
        return $months[$month];
    }

    /**
     * Update lampu.
     */
    public function update(Request $request, $id)
    {
        $lampu = Lampu::findOrFail($id);
        
        $request->validate([
            'nama_lampu' => 'required|string|max:255',
            'lokasi' => 'required|string|max:100',
        ]);

        $lampu->update([
            'nama_lampu' => $request->nama_lampu,
            'lokasi' => $request->lokasi,
        ]);

        return redirect()->route('lampu.index')->with('success', 'Lampu berhasil diperbarui!');
    }

    /**
     * Hapus lampu.
     */
    public function destroy($id)
    {
        $lampu = Lampu::findOrFail($id);
        $lampu->delete();
        return redirect()->route('lampu.index')->with('success', 'Lampu berhasil dihapus!');
    }
}