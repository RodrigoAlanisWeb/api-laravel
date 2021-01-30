<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Passport;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $attr = $this->validateLogin($request);

        if (!Auth::attempt($attr)) {
            return response()->json(['succes' => false]);
        }

        return response()->json(['success' => true, 'auth' => true, 'token' => $this->getPersonalAccessToken()]);
    }

    public function signup(Request $request)
    {
        $attr = $this->validateSingup($request);

        $user = User::create([
            'name' => $attr['name'],
            'username' => $attr['username'],
            'email' => $attr['email'],
            'password' => Hash::make($attr['password'])
        ]);

        $url = $request->file('image')->store('public');

        Image::create([
            'imageable_type'=>'App\Models\User',
            'imageable_id' => $user->id,
            'url' => $url,
        ]);

        Auth::attempt(['email' => $attr['email'], 'password' => $attr['password']]);

        return response()->json([
            'success' => true, 'auth' => true,
            'msg' => 'User Created Successfully', 'token' => $this->getPersonalAccessToken()
        ]);
    }

    public function profile()
    {
        $user = Auth::user();
        return response()->json(['success'=>true,'user'=>$user]);
    }

    public function getImage()
    {
        $user = Auth::user();
        $url = Storage::url($user->image->url);
        return "<img src='http://127.0.0.1:8003$url'>";
    }

    public function logout()
    {
        Auth::user()->token()->revoke();

        return response()->json(['success' => true]);
    }

    public function validateLogin($request)
    {
        return $request->validate([
            'email' => 'required|email|string|max:255',
            'password' => 'required|string|min:6'
        ]);
    }

    public function getPersonalAccessToken()
    {
        if (request()->remember_me = true) {
            Passport::personalAccessTokensExpireIn(now()->addDays(15));
        }

        return Auth::user()->createToken('Personal Access Token');
    }

    public function validateSingup($request)
    {
        return $request->validate([
            'name' => 'required|string',
            'username' => 'required',
            'email' => 'required|email|string|max:255',
            'password' => 'required|string|min:6'
        ]);
    }
}
