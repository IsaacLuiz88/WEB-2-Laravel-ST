<?php

namespace App\Http\Controllers;

use \App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index()
    {
        $users = User::paginate(10); // Paginação para 10 usuários por página
        return view('users.index', compact('users'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $user->update($request->only('name', 'email'));

        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(string $id)
    {
        //
    }

    public function editRole(User $user)
    {
        $this->authorize('updateRole', $user); // Verifica se o usuário logado pode editar o papel deste $user

        $roles = ['admin', 'librarian', 'client']; // Papéis disponíveis
        return view('users.edit_role', compact('user', 'roles'));
    }

    public function updateRole(Request $request, User $user)
    {
        $this->authorize('updateRole', $user); // Verifica novamente antes de atualizar

        $request->validate([
            'role' => ['required', 'string', Rule::in(['admin', 'librarian', 'client'])],
        ]);

        $user->update(['role' => $request->role]);

        return redirect()->route('users.index')->with('success', 'User role updated successfully.');
    }
}
