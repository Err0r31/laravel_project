<?php

namespace App\Http\Controllers\APIController;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $redirect = '/'; 
    public function signup()  {
        // return view('auth.signup');
    }

    public function registr(Request $request) {
        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:App\Models\User',
            'password'=>'required|min:6'
        ]);

        $response = [
            'name'=>$request->name,
            'email'=>$request->email,
        ];

        // return response()->json($response);

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'role'=>'reader',
        ]);
        $token = $user->createToken('myAppToken')->plainTextToken;
        $user->remember_token = $token;
        $user->save();
        return response()->json($user); 
    }

    public function login() {
        // return view('auth.login');
    }

    public function authentication(Request $request) {
        $credentials = $request->validate([
            'email'=>'required|email',
            'password'=>'required|min:6'
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            // $request->session()->regenerate();
            $token = $request->user()->createToken('MyAppToken')->plainTextToken;
            return response()->json($token);
        }

        // return back()->withErrors([
        //     'email' => 'The provided credentials do not match our records.',
        // ])->onlyInput('email');
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();
        return response('Logout');
    }
}