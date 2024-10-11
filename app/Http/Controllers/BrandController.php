<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brand = Brand::all();

        return response()->json([
            'status' => 200,
            'result' => $brand
        ]);
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
        try {
            $validator = $request->validate([
                'name' => 'required',
                'slug' => 'required|unique:brands',
                'image' => 'required|image|mimes:png,jpg,jpeg', //|image|mimes:jpeg,png,jpg,gif,svg
            ]);

            $uploadFolder = 'brands';
            $path = $request->file('image')->store($uploadFolder, 'public');
            $url = asset(Storage::url($path));
            $brand = Brand::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'image' => $url,  // Simpan URL gambar di database
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Brand created successfully',
                'data' => $brand
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors(),
            ], 422);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $brand = Brand::findOrFail($id);

            if (!$brand) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Brand not found',
                ]);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Brand retrieved successfully',
                'result' => $brand
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = $request->validate([
                'name' => 'required',
                'slug' => 'required|unique:brands,slug,' . $id, //biarkan jika id nya sama dengan slug yang ada
                'image' => 'sometimes|image|mimes:png,jpg,jpeg', //sometimes agar masih bisa jalan walau tidak ada image
            ]);

            // cari id dari url
            $brand = Brand::find($id);

            // cek apakah ada gambar
            if (!$request->hasFile('image')) {
                // jika tidak update dengan gambar lama
                $brand->update([
                    'name' => $request->name,
                    'slug' => $request->slug
                ]);

               
            } else {
                // jika ada hapus gambar lama
                if($brand->image) {
                    $oldImagePath = 'brands/' . basename( $brand->image);
                    Storage::disk('public')->delete($oldImagePath);
                }

                // masukkan gambar baru 
                $path = $request->file('image')->store('brands', 'public');
                $url = asset(Storage::url($path));

                // update semua request
                $brand->update([
                    'name' => $request->name,
                    'slug' => $request->slug,
                    'image' => $url,
                ]);
                
            }

            // kembalikan response
            return response()->json([
                'status' => 200,
                'message' => 'Brand updated successfully',
                'result' => $brand
            ], 200);
            
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors(),
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json([
                'status' => 404,
                'message' => 'Brand not found',
            ], 404);
        }

        $brand->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Brand deleted successfully',
        ]);
    }
}
