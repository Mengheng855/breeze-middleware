<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'auth_provider' => 'google',
                    'auth_provider_id' => $googleUser->getId(),
                    'password' => bcrypt('defaultpassword'), // dummy password
                ]
            );

            Auth::login($user);

            return redirect()->intended('/dashboard');
        } catch (Exception $e) {
            return redirect()->route('login')->withErrors(['msg' => 'Google login failed']);
        }
    }
}
