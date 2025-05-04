<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Lampu;

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
        ]);

        Jadwal::create([
            'lampu_id' => $request->lampu_id,
            'hari' => $request->hari,
            'waktu_nyala' => $request->waktu_nyala,
            'waktu_mati' => $request->waktu_mati,
        ]);

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
        ]);

        $jadwal->update([
            'lampu_id' => $request->lampu_id,
            'hari' => $request->hari,
            'waktu_nyala' => $request->waktu_nyala,
            'waktu_mati' => $request->waktu_mati,
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
}
