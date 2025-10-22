<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->isActive()) {
            return back()->withErrors(['email' => 'Usuário não encontrado ou inativo']);
        }

        if ($this->checkPassword($request->password, $user->senha)) {
            Auth::login($user, $request->boolean('remember'));
            return redirect()->route($user->dashboardRoute);
        }

        return back()->withErrors(['email' => 'Credenciais inválidas']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect()->route('login');
    }

    private function checkPassword($password, $hash): bool
    {
        return Hash::check($password, $hash) || md5($password) === $hash || $password === $hash;
    }
}
