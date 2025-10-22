<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('workspace.index');
            }
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
    
        // Mapear password para senha (campo do banco)
        $loginData = [
            'email' => $credentials['email'],
            'password' => $credentials['password']
        ];
    
        
        if (Auth::attempt($loginData, $request->filled('remember'))) {
            \Log::info('AUTH SUCCESS!');
            
            $request->session()->regenerate();
            
            $user = auth()->user();
            \Log::info('User authenticated:', ['id' => $user->id, 'nome' => $user->nome, 'isAdmin' => $user->isAdmin()]);
            
            if ($user->isAdmin()) {
                \Log::info('Redirecting to admin.dashboard');
                return redirect()->route('admin.dashboard');
            } else {
                \Log::info('Redirecting to workspace.index');
                return redirect()->route('workspace.index');
            }
        }
        
        
        return back()->with('error', 'Credenciais inválidas');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|min:6'
        ]);

        User::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'senha' => Hash::make($request->password),
            'nivel_acesso' => 'usuario',
            'ativo' => 1
        ]);

        return redirect()->route('login')->with('success', 'Conta criada com sucesso!');
    }
}
