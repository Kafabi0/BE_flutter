<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User; // Laravel 7 default User model
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    // ---------------- Register ----------------
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',
            'password' => 'required|min:4',
            'no_hp' => 'required|numeric',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'usia' => 'required|integer|min:1',
        ]);

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'no_hp' => $request->no_hp,
            'jenis_kelamin' => $request->jenis_kelamin,
            'usia' => $request->usia,
            'profile_image' => null, // default
        ]);

        return response()->json(['status'=>'success','user'=>$user]);
    }

    // ---------------- Login ----------------
    public function login(Request $request)
    {
        $user = User::where('username', $request->username)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            return response()->json(['status'=>'success','user'=>$user]);
        }

        return response()->json(['status'=>'error','message'=>'Username atau password salah'], 401);
    }

    // ---------------- Update Profil ----------------
    public function updateProfile(Request $request)
{
    $request->validate([
        'username' => 'sometimes|exists:users,username',
        'password' => 'sometimes|min:4',
        'no_hp' => 'sometimes|numeric',
        'profile_image' => 'sometimes|file|image|max:2048',
    ]);

    // Ambil user berdasarkan username (misal dikirim dari Flutter)
    $user = User::where('username', $request->username)->first();
    if (!$user) {
        return response()->json(['status'=>'error','message'=>'User tidak ditemukan'], 404);
    }

    if ($request->has('username')) $user->username = $request->username;
    if ($request->has('password') && $request->password != '') $user->password = Hash::make($request->password);
    if ($request->has('no_hp')) $user->no_hp = $request->no_hp;

    if ($request->hasFile('profile_image')) {
        $file = $request->file('profile_image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads'), $filename);
        $user->profile_image = $filename;
    }

    $user->save();

    // Tambahkan URL lengkap supaya Flutter bisa langsung menampilkan
    $user->profile_image_url = $user->profile_image ? url('uploads/'.$user->profile_image) : null;

    return response()->json(['status' => 'success', 'user' => $user]);
}


}
