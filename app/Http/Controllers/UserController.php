<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function loginPage(){
        return view('login');
    }

    public function loginVerification(Request $req){
        $credentials = [
            'email' => $req->email,
            'password' => $req->password,
        ];

        if($req->remember){
            Cookie::queue('email', $req->email, 100);
            Cookie::queue('password', $req->password, 100);
        }
    
        if (Auth::attempt($credentials, true)) {
            Session::put('credential', $credentials);
            return redirect()->intended('home');
        }
    
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(){
        Auth::logout();
        return redirect('/login');
    }

    public function registerVerification(Request $req){
        $rules = [
            'name' => 'required|min:5',
            'gender' => 'required',
            'address' => 'required|min:10',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'confirmPassword' => 'required|same:password',
            'agreement' => 'required'
        ];
        
        $validation = Validator::make($req->all(), $rules);

        if($validation->fails()){
            return back()->withErrors([$validation], 'insert');
        }
    }
}