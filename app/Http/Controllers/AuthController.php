<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate input
        // Create user
        // Return success response with token
    }

    public function login(Request $request)
    {
        // Validate input
        // Attempt to authenticate
        // Return token or error
    }

    public function userProfile()
    {
        // Return authenticated user profile
    }

    public function logout()
    {
        // Invalidate token
        // Return success message
    }
}
