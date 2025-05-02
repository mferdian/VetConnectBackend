<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Register
     public function register(Request $request)
     {
         $request->validate([
             'name' => 'required|string|max:255',
             'email' => 'required|string|email|max:255|unique:users,email',
             'password' => 'required|string|min:8|confirmed',
         ]);

         $user = User::create([
             'name' => $request->name,
             'email' => $request->email,
             'password' => Hash::make($request->password),
         ]);

         // Optional: Langsung login setelah register
         $token = $user->createToken('api-token')->plainTextToken;

         return response()->json([
             'message' => 'Register berhasil',
             'user' => $user,
             'token' => $token,
         ], 201);
     }

     // LOGIN
     public function login(Request $request)
     {
         $request->validate([
             'email' => 'required|email',
             'password' => 'required',
         ]);

         $user = User::where('email', $request->email)->first();

         if (! $user || ! Hash::check($request->password, $user->password)) {
             throw ValidationException::withMessages([
                 'email' => ['Email atau password salah'],
             ]);
         }

         $token = $user->createToken('api-token')->plainTextToken;

         return response()->json([
             'message' => 'Login berhasil',
             'user' => $user,
             'token' => $token,
         ]);
     }

     // LOGOUT
     public function logout(Request $request)
     {
         $request->user()->currentAccessToken()->delete();

         return response()->json([
             'message' => 'Logout berhasil',
         ]);
     }

     // GET USER PROFILE
     public function me(Request $request)
     {
         return response()->json([
             'user' => $request->user(),
         ]);
     }
}
