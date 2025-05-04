<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Lampu;
use Carbon\Carbon;

class JadwalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jadwal = Jadwal::with('lampu')->get();
        $lampu = Lampu::all();
        return view('dashboard.jadwal', compact('jadwal', 'lampu'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'lampu_id' => 'required|exists:lampu,id',
            'hari' => 'required|string',
            'waktu_nyala' => 'required',
            'waktu_mati' => 'required',
            'frekuensi' => 'nullable|string',
            'repeat_days' => 'nullable|array',
            'intensitas' => 'required|integer|min:0|max:100',
        ]);
        
        // Jika frekuensi adalah 'daily' atau 'once', gunakan hari yang dipilih
        if ($request->frekuensi == 'daily' || $request->frekuensi == 'once' || !$request->frekuensi) {
            Jadwal::create([
                'lampu_id' => $request->lampu_id,
                'hari' => $request->hari,
                'waktu_nyala' => $request->waktu_nyala,
                'waktu_mati' => $request->waktu_mati,
                'frekuensi' => $request->frekuensi ?? 'once',
                'intensitas' => $request->intensitas,
            ]);
        } 
        // Jika frekuensi adalah 'weekly', buat jadwal untuk setiap hari yang dipilih
        else if ($request->frekuensi == 'weekly' && $request->has('repeat_days')) {
            $dayNames = [
                0 => 'Minggu',
                1 => 'Senin',
                2 => 'Selasa',
                3 => 'Rabu',
                4 => 'Kamis',
                5 => 'Jumat',
                6 => 'Sabtu',
            ];
            
            foreach ($request->repeat_days as $day) {
                Jadwal::create([
                    'lampu_id' => $request->lampu_id,
                    'hari' => $dayNames[$day],
                    'waktu_nyala' => $request->waktu_nyala,
                    'waktu_mati' => $request->waktu_mati,
                    'frekuensi' => $request->frekuensi,
                    'intensitas' => $request->intensitas,
                ]);
            }
        }
        // Jika frekuensi adalah 'monthly', buat jadwal bulanan
        else if ($request->frekuensi == 'monthly') {
            Jadwal::create([
                'lampu_id' => $request->lampu_id,
                'hari' => $request->hari,
                'waktu_nyala' => $request->waktu_nyala,
                'waktu_mati' => $request->waktu_mati,
                'frekuensi' => 'monthly',
                'tanggal_bulanan' => Carbon::now()->day,
                'intensitas' => $request->intensitas,
            ]);
        }

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $jadwal = Jadwal::findOrFail($id);
        
        $request->validate([
            'lampu_id' => 'required|exists:lampu,id',
            'hari' => 'required|string',
            'waktu_nyala' => 'required',
            'waktu_mati' => 'required',
            'intensitas' => 'required|integer|min:0|max:100',
        ]);

        $jadwal->update([
            'lampu_id' => $request->lampu_id,
            'hari' => $request->hari,
            'waktu_nyala' => $request->waktu_nyala,
            'waktu_mati' => $request->waktu_mati,
            'intensitas' => $request->intensitas,
        ]);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->delete();
        
        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil dihapus!');
    }
    
    /**
     * Jalankan jadwal berdasarkan hari dan waktu saat ini
     */
    public function executeSchedule()
    {
        $now = Carbon::now();
        $currentTime = $now->format('H:i');
        $currentDay = $now->translatedFormat('l');
        
        // Dapatkan jadwal yang cocok dengan hari dan waktu saat ini
        $jadwals = Jadwal::with('lampu')
            ->where(function($query) use ($currentDay) {
                $query->where('hari', $currentDay)
                    ->orWhere('hari', 'Setiap Hari');
            })
            ->get();
            
        foreach ($jadwals as $jadwal) {
            // Cek apakah waktu nyala atau mati sesuai dengan waktu saat ini
            if ($currentTime == substr($jadwal->waktu_nyala, 0, 5)) {
                // Nyalakan lampu dengan intensitas yang telah ditentukan
                $lampu = $jadwal->lampu;
                $lampu->status = 1;
                $lampu->intensitas = $jadwal->intensitas;
                $lampu->save();
            } 
            else if ($currentTime == substr($jadwal->waktu_mati, 0, 5)) {
                // Matikan lampu
                $lampu = $jadwal->lampu;
                $lampu->status = 0;
                $lampu->intensitas = 0;
                $lampu->save();
            }
        }
        
        return response()->json(['success' => true, 'message' => 'Jadwal telah diproses']);
    }
}
