<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;


class AuthenticationController extends Controller
{
    public function login(Request $request)
    {
        $validatedInputs = request()->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string']
        ]);

        $users = User::latest()->where('email', $validatedInputs['email'])->get();

        if (count($users) == 0 || !Hash::check($validatedInputs['password'], $users[0]->password)) {
            return response([
                'message' => "Authentication Failed",
                'errors' => [
                    'email' => ['email or password does not match'],
                    'password' => ['email or password does not match'],
                ]
            ], 403);
        } else {
            $token = $users[0]->createToken($users[0]->name)->plainTextToken;
            return response([
                'message' => "Authentication Successful",
                'errors' => false,
                'payload' => [
                    'token' => $token
                ],
            ], 200);
        }
    }

    public function logout(Request $request)
    {
        $validatedInputs = request()->validate([
            'token' => ['required', 'string'],
        ]);
        $hashedToken = $validatedInputs['token'];
        try {
            $token = PersonalAccessToken::findToken($hashedToken);
            $user = $token->tokenable;
            $user->tokens()->delete();
        } catch (Exception $e) {
            return response([
                'message' => "Invalid Token",
                'errors' => [
                    'token' => [$e->getMessage()]
                ],
            ], 422);
        }
        $user = $token->tokenable;
        $user->tokens()->delete();

        return response([
            'message' => "Logged out successfully. Please re-authenticate to use services",
            'errors' => false,
        ], 200);
    }
}
