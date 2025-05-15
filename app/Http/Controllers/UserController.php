<?php

namespace App\Http\Controllers;

use Hash;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
class UserController extends Controller
{

    public function get1(){
        return "user/home doing perfecctlyyyyy";
    }
    public function register(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|confirmed'
            ]);

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password'])
            ]);

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422); // HTTP 422 Unprocessable Entity
        }
    }
    public function login(Request $request)
    {
        try {
            // $validatedData = $request->validate([
            //     'email' => 'required|string|email',
            //     'password' => 'required|string'
            // ]);
            if (!Auth::attempt($request->only('email', 'password')))
                return response()->json(
                    [
                        'masseage' => 'invalid email or password'
                    ],
                    401
                );
            $user = User::where('email', $request->email)->firstOrFail();
            $token = $user->createToken('auth_Token')->plainTextToken;
            return response()->json([
                'message' => 'Login succusful',
                'User' => $user,
                'Tokem' => $token,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422); // HTTP 422 Unprocessable Entity
        }
    }
    public function logout(Request $request)
    {
        //  $request->user()->currentAccessesToken()->delete();
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'logout Successful'
        ]);
    }
    public function login1(Request $request)
{
return "hamza fattouh";
}


}
