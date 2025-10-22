<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::orderBy('id', 'desc')->get();
            return view('admin.users.index', compact('users'));
        } catch (\Exception $e) {
            return 'Erro: ' . $e->getMessage();
        }
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        try {
            User::create([
                'nome' => $request->nome,
                'email' => $request->email,
                'senha' => Hash::make($request->senha),
                'nivel_acesso' => $request->nivel_acesso,
                'telefone' => $request->telefone,
                'ativo' => 1
            ]);

            return redirect()->route('admin.users.index')->with('success', 'Usuário criado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao criar: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return view('admin.users.show', compact('user'));
        } catch (\Exception $e) {
            return back()->with('error', 'Usuário não encontrado');
        }
    }

    public function edit($id)
    {
        try {
            $user = User::findOrFail($id);
            return view('admin.users.edit', compact('user'));
        } catch (\Exception $e) {
            return back()->with('error', 'Usuário não encontrado');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            $user->update([
                'nome' => $request->nome,
                'email' => $request->email,
                'nivel_acesso' => $request->nivel_acesso,
                'telefone' => $request->telefone,
                'senha' => $request->filled('senha') ? Hash::make($request->senha) : $user->senha
            ]);

            return redirect()->route('admin.users.index')->with('success', 'Usuário atualizado!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            
            if ($user->id == auth()->id()) {
                return back()->with('error', 'Você não pode deletar sua própria conta!');
            }

            $user->delete();
            return redirect()->route('admin.users.index')->with('success', 'Usuário deletado!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao deletar usuário');
        }
    }

    public function toggleStatus($id)
    {
        try {
            $user = User::findOrFail($id);
            
            if ($user->id == auth()->id()) {
                return back()->with('error', 'Você não pode desativar sua própria conta!');
            }

            $user->update(['ativo' => !$user->ativo]);
            $status = $user->ativo ? 'ativado' : 'desativado';
            
            return back()->with('success', "Usuário {$status}!");
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao alterar status');
        }
    }
}