<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:4',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return API_ERROR('Invalid credentials.', null, 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return API_SUCCESS('Login successful.', [
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->RequestValidator($request);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            return API_SUCCESS('User has been successfully created.', [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]);
        } catch (\Exception $e) {
            return API_ERROR('Unable to create user. Please try again.', ['error' => $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return API_SUCCESS('Logout Successfully');
    }

    private function RequestValidator($request, ?int $id = null)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
        ];

        if (!$id) {
            $rules['email'] .= '|unique:users,email';
        }

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:4';
            $rules['confirm_password'] = 'same:password';
        }

        $messages = [
            'name.required' => 'The name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.unique' => 'Email already exists.',
            'password.min' => 'Password must be at least 4 characters.',
            'confirm_password.same' => 'Confirm password must match the password.',
        ];

        return $request->validate($rules, $messages);
    }
}
