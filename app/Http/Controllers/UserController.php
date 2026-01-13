<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
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

    public function update(Request $request, $user_id)
    {

        $user = User::findOrFail($user_id);
        $validated = $this->RequestValidator($request, $user->id);

        try {
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => isset($validated['password']) ? Hash::make($validated['password']) : $user->password,
            ]);

            return API_SUCCESS('User has been successfully updated.', [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]);
        } catch (\Exception $e) {
            return API_ERROR('Unable to update user. Please try again.', ['error' => $e->getMessage()], 500);
        }
    }


    public function delete(int $id)
    {
        User::findOrFail($id)->delete();
        try {
            return API_SUCCESS('User is successfully deleted.');
        } catch (\Exception $e) {
            return API_ERROR('Unable to update user. Please try again.', ['error' => $e->getMessage()], 500);
        }
    }

    public function profile()
    {
        $user = auth()->user();

        if (!$user) {
            return API_ERROR('Unauthorized', 401);
        }

        return API_SUCCESS('Profile fetched.', [
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ]
        ]);
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
