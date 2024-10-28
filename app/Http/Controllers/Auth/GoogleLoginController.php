<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        // Google user object dari google
        $userFromGoogle = Socialite::driver('google')->user();

        $email = $userFromGoogle->getEmail();
        list($user, $domain) = explode("@", $email);

        if ($domain != "polines.ac.id") {
            return redirect('/')->with(['error' => 'Email wajib menggunakan domain @polines.ac.id']);
        }

        // Ambil user dari database berdasarkan google mail
        $getEmail = User::where('email', $userFromGoogle->getEmail())->first();

        // Jika tidak ada user, maka buat user baru
        if (!$getEmail) {
            return redirect('/')->with(['error' => 'Email tidak terdaftar']);
        }

        // Jika ada user langsung login saja
        auth('web')->login($getEmail);
        session()->regenerate();
        return redirect('/');
    }
}
