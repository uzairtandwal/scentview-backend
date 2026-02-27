<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Flutter side se 'name' bhi aa raha hai, isliye validate karna zaroori hai
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'user', // Default role user hi rahega
        ]);

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id'           => $user->id,
                'name'         => $user->name,
                'email'        => $user->email,
                'role'         => $user->role,
                'photo_url'    => $user->profile_photo_url, // Agar use kar rahe hain
                'phone_number' => $user->phone_number,
                'address'      => $user->address,
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Incorrect email or password. Please try again.'
            ], 401);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id'           => $user->id,
                'name'         => $user->name,
                'email'        => $user->email,
                'role'         => $user->role,
                'photo_url'    => $user->profile_photo_url,
                'phone_number' => $user->phone_number,
                'address'      => $user->address,
            ],
        ]);
    }

    public function me(Request $request)
    {
        $u = $request->user();
        return response()->json([
            'id'           => $u->id,
            'name'         => $u->name,
            'email'        => $u->email,
            'role'         => $u->role,
            'photo_url'    => $u->profile_photo_url,
            'phone_number' => $u->phone_number,
            'address'      => $u->address,
            'created_at'   => $u->created_at,
        ]);
    }
public function updateFcmToken(Request $request) {
    $request->validate([
        'user_id' => 'required',
        'fcm_token' => 'required'
    ]);

    $user = User::find($request->user_id);
    if($user) {
        $user->fcm_token = $request->fcm_token;
        $user->save();
        return response()->json(['status' => 'success', 'message' => 'Token Updated']);
    }

    return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
}
    public function logout(Request $request)
    {
        // Saare tokens delete karne ke liye (Safety first)
        $request->user()->tokens()->delete();
        return response()->json(['status' => 'ok', 'message' => 'Logged out successfully']);
    }
}