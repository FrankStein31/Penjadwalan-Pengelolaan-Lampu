<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lampu;

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

        return response()->json(['success' => true]);
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