<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public static function login(string $login, string $password, ?bool $remember = false): bool {
        $loginType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        return Auth::attempt([$loginType => $login, 'password' => $password], $remember);
    }
}
