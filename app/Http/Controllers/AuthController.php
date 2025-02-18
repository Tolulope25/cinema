<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function register(){
        return view('auth.register');
    }
    public function login(){
        return view('auth.login');
    }



    public function registerUser(Request $request) {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255|regex:/^[a-zA-Z]+$/',
            'last_name' => 'required|string|max:255|regex:/^[a-zA-Z]+$/',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        $formFields = [
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => 'user',
        ];

        $user = User::create($formFields);
        if($user){
            auth()->login($user);
            if($user->id == 1) {  // First user becomes admin
                $user->role = 'admin';
                $user->save();
            }

        return redirect('/')->with('success', 'User has been registered successfully');
        } else  {
            return redirect()->back()->withInput()->withErrors(['error' => 'Registration failed']);
        }


    }
    public function loginUser(Request $request) {
        // Debug: Print all input
        \Log::info('Login Attempt', [
            'email' => $request->email,
            'all_input' => $request->all()
        ]);

        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validator->fails()) {
            // Detailed validation error logging
            \Log::error('Validation Failed', [
                'errors' => $validator->errors()->toArray(),
                'input' => $request->all()
            ]);
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }

        // Debug: Check user existence
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            \Log::warning('User Not Found', [
                'email' => $request->email
            ]);
            return redirect()->back()->withInput()->with('error', 'No user found with this email');
        }

        // Debug: Password verification
        if (!Hash::check($request->password, $user->password)) {
            \Log::warning('Password Check Failed', [
                'email' => $request->email,
                'password_hash' => $user->password
            ]);
            return redirect()->back()->withInput()->with('error', 'Invalid credentials');
        }

        // Ensure logging is working
        \Log::info('Login Successful', ['email' => $user->email]);

        $user->update([
            'last_login' => now(),

        ]);

        // Log the successful login with additional last login details
        \Log::info('Login Successful', [
            'email' => $user->email,
            'last_login' => $user->last_login_at,
        
        ]);

        auth()->login($user);

        return redirect('/')->with('success', 'User logged in successfully');
    }
    public function logout(Request $request){
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success','Logout successful');

    }

}



