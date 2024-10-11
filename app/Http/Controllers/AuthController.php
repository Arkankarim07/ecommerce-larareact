<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request) {
        try {
            
        $validate = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 404,
                'message' => 'Invalid credentials'
            ], 404);
        }

        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json([
            'status' => 200,
            'message' => 'Login successful',
            'token' => $token
        ], 200);

    } catch(ValidationException $e) {
        return response()->json([
            'status' => 404,
            'message' => $e->errors(),
        ], 404);
    }
    }
}
