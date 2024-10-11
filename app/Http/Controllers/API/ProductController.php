<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product = Product::with('brand')->get();

        return response()->json([
            'status' => 200,
            'message' => 'Product retrieved successfully',
            'result' => $product
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'brand_id' => 'required|exists:brands,id',
            'detail' => 'required',
            'price' => 'required|numeric|min:0',
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validatedData = $validator->validated();
        $product = Product::create($validatedData);

        return response()->json([
            'status' => 200,
            'message' => 'Product created successfully',
            'data' => $product
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product =  Product::with('brand')->findOrFail($id);

        return response()->json([
            'status' => 200,
            'message' => 'Product retrieved successfully',
            'result' => $product
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'detail' => 'required',
            'price' => 'required|numeric|min:0|decimal:0,2|integer',
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validatedData = $validator->validated();

        $product->name = $validatedData['name'];
        $product->detail = $validatedData['detail'];
        $product->price = $validatedData['price'];

        $product->save();

        return response()->json([
            'status' => 200,
            'message' => 'Product updated successfully',
            'data' => $product
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->findOrFail($product->id)->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Product deleted successfully',
            'data' => $product
        ], 200);
        
    }

    public function bulkDestroy(Request $request) {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array', // Ensure 'ids' is required and is an array
            'ids.*' => 'integer|exists:products,id',
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validatedData = $validator->validated();
        Product::whereIn('id', $validatedData['ids'])->delete();
        
        return response()->json([
            'status' => 200,
            'message' => 'Products bulk deleted successfully',
        ]);
    }
}
