<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }


    public function update(Request $request)
    {
        $user = Auth::user();



        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'no_telp' => 'nullable|string|max:15',
            'umur' => 'nullable|integer|min:1',
        ]);




        $userModel = User::find($user->id);

        $userModel->name = $request->name;
        $userModel->email = $request->email;
        $userModel->no_telp = $request->no_telp;
        $userModel->umur = $request->umur;

        if ($request->password) {
            $userModel->password = Hash::make($request->password);
        }


        if ($request->hasFile('profile_photo')) {
            if ($userModel->profile_photo) {
                Storage::delete($userModel->profile_photo);
            }
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $userModel->profile_photo = $path;
        }

        $userModel->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $userModel, // <- ini data yang sudah diupdate
        ]);
    }
}
