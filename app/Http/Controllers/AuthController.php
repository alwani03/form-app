<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\LogActivity;
use App\Enums\ActivityType;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
           
            // Log Activity
            LogActivity::create([
                'user_id' => $user->id,
                'remark' => ActivityType::LOGIN->generateRemark('User', $user->username),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;
            // Generate dummy cookie values as per image example (long hashes)
            $hwePuss = hash('sha256', $token . 'puss');
            $hweUss = hash('sha256', $token . 'uss');

            // Set cookies
            // Note: Domain is set to null to work on localhost. 
            // If strictly following the image, we would set .pinjamyuk.co.id but that won't work on localhost.
            // I'll leave domain default.
            $cookie1 = cookie('HWE_PUSS', $hwePuss, 60 * 24 * 365, '/', null, true, true);
            $cookie2 = cookie('HWE_USS', $hweUss, 60 * 24 * 365, '/', null, true, true);

            return response()->json([
                'message' => 'Login success',
                'user' => $user,
                'token' => $token
            ])->withCookie($cookie1)->withCookie($cookie2);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function logout(Request $request)
    {
        // Log Activity before deleting token
        if ($user = $request->user()) {
            LogActivity::create([
                'user_id' => $user->id,
                'remark' => ActivityType::LOGOUT->generateRemark('User', $user->username),
            ]);

            $user->currentAccessToken()->delete();
        }
        
        $cookie1 = cookie()->forget('HWE_PUSS');
        $cookie2 = cookie()->forget('HWE_USS');

        return response()->json(['message' => 'Logged out'])
            ->withCookie($cookie1)
            ->withCookie($cookie2);
    }
}
