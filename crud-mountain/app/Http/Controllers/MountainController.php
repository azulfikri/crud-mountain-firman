<?php

namespace App\Http\Controllers;

use App\Models\Mountain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MountainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $mountains = Mountain::all();
        return response()->json([
            'data' => $mountains,
            'message' => 'Berhasil Menampilkan daftar gunung'
        ], 200);
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
        //
        try {
            //code...
            $validated = $request->validate([
                'name' => 'required|unique:mountains',
                'location' => 'required',
                'height' => 'required',
                'type' => 'required',
                'status' => 'required',
                'difficulty' => 'required',
                'routes' => 'required',
                'description' => 'nullable',
                'image' => 'nullable|image|max:2048',
            ]);

            // Simpan gambar jika ada
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('images/mountains', 'public');

                // $validated['image_url'] = Storage::url($path); // Hasil: /storage/images/mountains/xxx.jpg
                $validated['image_url'] = $path; // Hasil: /storage/images/mountains/xxx.jpg
            }

            $mountain = Mountain::create($validated);

            return response()->json([
                'data' => [
                    'id' => $mountain->id,
                    'name' => $mountain->name,
                    'location' => $mountain->location,
                    'height' => $mountain->height,
                    'type' => $mountain->type,
                    'status' => $mountain->status,
                    'difficulty' => $mountain->difficulty,
                    'routes' => $mountain->routes,
                    'description' => $mountain->description,
                    'image_url' => $mountain->image_url,
                    'created_at' => $mountain->created_at,
                    'updated_at' => $mountain->updated_at,
                ],
                'message' => 'Gunung berhasil ditambahkan.'
            ], 201);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'message' => 'Gagal menyimpan data gunung',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Mountain $mountain)
    {
        //
        try {
            return response()->json($mountain, 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'message' => 'Gagal menampilkan data gunung',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mountain $mountain)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mountain $mountain)
    {
        //
        try {
            //code...
            $validated = $request->validate([
                'name' => 'required|unique:mountains,name,' . $mountain->id,
                'location' => 'required',
                'height' => 'required',
                'type' => 'required',
                'status' => 'required',
                'difficulty' => 'required',
                'routes' => 'required',
                'description' => 'nullable',
                'image' => 'nullable|image|max:2048',
            ]);

            // Hapus gambar lama dan upload baru jika ada
            if ($request->hasFile('image')) {
                if ($mountain->image_url) {
                    Storage::disk('public')->delete($mountain->image_url);
                }
                $validated['image_url'] = $request->file('image')->store('images/mountains', 'public');
            }

            $mountain->update($validated);

            return response()->json($mountain);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'message' => 'Gagal memperbarui data gunung',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mountain $mountain)
    {
        //
        try {
            //code...
            if ($mountain->image_url) {
                Storage::disk('public')->delete($mountain->image_url);
            }

            $mountain->delete();

            return response()->json(['message' => 'Data deleted successfully.']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'message' => 'Gagal menghapus data gunung',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
