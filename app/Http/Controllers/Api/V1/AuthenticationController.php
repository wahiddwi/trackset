<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    public function login(Request $request)
    {

        $request->validate([
            'usr_nik' => 'required',
            // 'password' => 'required'
        ]);

        $user = User::where('usr_nik', $request->usr_nik)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessage([
                'email' => ['The provided creadentials are incorrect.'],
            ]);
        }
        return $user->createToken('auth_token')->plainTextToken;
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
