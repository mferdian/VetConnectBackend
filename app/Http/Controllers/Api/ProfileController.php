<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
     // Get User Profile
     public function show(Request $request)
     {
         return response()->json([
             'user' => $request->user()
         ]);
     }

     // Update User Profile
     public function update(Request $request)
     {
         $user = $request->user();

         $request->validate([
             'name' => 'required|string|max:255',
             'email' => 'required|email|max:255|unique:users,email,' . $user->id,
             'password' => 'nullable|string|min:8|confirmed',
             'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
             'no_telp' => 'nullable|string|max:15',
             'umur' => 'nullable|integer|min:1',
         ]);

         $user->name = $request->name;
         $user->email = $request->email;
         $user->no_telp = $request->no_telp;
         $user->umur = $request->umur;

         if ($request->password) {
             $user->password = Hash::make($request->password);
         }

         if ($request->hasFile('profile_photo')) {
             if ($user->profile_photo) {
                 Storage::disk('public')->delete($user->profile_photo);
             }
             $path = $request->file('profile_photo')->store('profile_photos', 'public');
             $user->profile_photo = $path;
         }

         $user->save();

         return response()->json([
             'message' => 'Profil berhasil diperbarui',
             'user' => $user
         ]);
     }

     // Delete Account
     public function destroy(Request $request)
     {
         $user = $request->user();

         if ($user->profile_photo) {
             Storage::disk('public')->delete($user->profile_photo);
         }

         $user->delete();

         return response()->json([
             'message' => 'Akun berhasil dihapus'
         ]);
     }
}
