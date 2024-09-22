<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Register a new user and return a JWT token.
     */
    public function register(Request $request)
    {
        // Normalize gender input to boolean
        $request->merge([
            'gender' => filter_var($request->gender, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
        ]);

        // Validate the incoming request data
        $validatedData = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|string|email|max:255|unique:users',
            'password'       => 'required|string|min:6|confirmed',
            'address'        => 'required|string',
            'gender'         => 'required|boolean',
            'marital_status' => 'required|string|in:single,married,divorced,widowed',
        ]);

        // Proceed with user creation using $validatedData
        $user = User::create([
            'name'           => $validatedData['name'],
            'email'          => $validatedData['email'],
            'password'       => Hash::make($validatedData['password']),
            'address'        => $validatedData['address'],
            'gender'         => $validatedData['gender'],
            'marital_status' => $validatedData['marital_status'],
        ]);

        // Generate a JWT token for the newly created user
        $token = JWTAuth::fromUser($user);

        // Return the user and token in the response
        return response()->json([
            'message' => 'User registered successfully',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }

    /**
     * Authenticate a user and return a JWT token.
     */
    public function login(Request $request)
    {
        // Validate the login credentials
        $credentials = $request->only('email', 'password');

        $validator = Validator::make($credentials, [
            'email'    => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            // Attempt to verify the credentials and create a token
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
        } catch (JWTException $e) {
            // Something went wrong with generating the token
            return response()->json(['error' => 'Could not create token'], 500);
        }

        // Retrieve the authenticated user
        $user = auth()->user();

        // Return the user and token in the response
        return response()->json([
            'message' => 'Login successful',
            'user'    => $user,
            'token'   => $token,
        ], 200);
    }

    /**
     * Log out the authenticated user and invalidate the token.
     */
    public function logout(Request $request)
    {
        try {
            // Invalidate the token
            JWTAuth::invalidate(JWTAuth::parseToken());

            return response()->json([
                'message' => 'User logged out successfully'
            ], 200);
        } catch (JWTException $e) {
            // Something went wrong while invalidating the token
            return response()->json(['error' => 'Could not log out user'], 500);
        }
    }

    /**
     * Get the authenticated user's profile.
     */
    public function getProfile()
    {
        // Retrieve the authenticated user
        $user = auth()->user();

        // Return the user data
        return response()->json([
            'user' => $user
        ], 200);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function updateProfile(Request $request)
    {
        // Get the authenticated user
        $user = auth()->user();

        // Validate the incoming request data
        $validatedData = $request->validate([
            'name'           => 'sometimes|required|string|max:255',
            'email'          => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password'       => 'sometimes|required|string|min:6|confirmed',
            'address'        => 'sometimes|required|string',
            'gender'         => 'sometimes|required|boolean',
            'marital_status' => 'sometimes|required|string|in:single,married,divorced,widowed',
        ]);

        // Update user profile
        if (isset($validatedData['password'])) {
            // Hash the password if it's being updated
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        // Update the user in the database
        $user->update($validatedData);

        // Return the updated user profile
        return response()->json([
            'message' => 'Profile updated successfully',
            'user'    => $user,
        ], 200);
    }
}
