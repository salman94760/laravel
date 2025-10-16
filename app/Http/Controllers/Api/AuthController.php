<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\UsersInfo;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validation (aap apne hisaab se fields add kar sakte hain)
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            // password yahan nahi le rahe, kyunki hum generate karenge
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Password generate karna (random 10 characters ka)
        $password = Str::random(10);

        // User create karna
        $user = User::create([
            'name'      => $request->fullname,
            'email'     => $request->email,
            'password'  => Hash::make($password),
        ]);

        if($user->id){
            UsersInfo::create([
                'userId'        => $user->id,
                'fullname'      => $request->fullname,
                'address'       => $request->address,
                'phone'         => $request->phone,
                'passwordhint'  => $password,
                'zipcode'       => '',
                'landmark'      => ''
            ]);
        }

        // Optional: password user ko wapas bhejna response mein
        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'password' => $password,  // client ko batana hoga ki ye password use kare
        ], 201);
    }

    public function login(Request $request){
        // Validate request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Credentials prepare karo
        $credentials = $request->only('email', 'password');

        // Attempt to login
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Agar token based system use kar rahe ho (like Sanctum)
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password',
            ], 401);
        }
    }
}
