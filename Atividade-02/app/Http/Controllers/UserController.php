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
        $this->middleware('can:viewAny,App\Models\User')->only('index');
        $this->middleware('can:create,App\Models\User')->only(['create', 'store']);
        $this->middleware('can:update,user')->only(['edit', 'update']);
        $this->middleware('can:delete,user')->only('destroy');
        $this->middleware('can:updateRole,user')->only('update');
        $this->middleware('can:viewDebits,App\Models\User')->only(['listDebtors', 'clearDebit']);
        $this->middleware('can:clearDebit,user')->only('clearDebit');
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

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
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

    public function listDebtors()
    {
        $this->authorize('viewDebits', User::class);

        $debtors = User::where('debit', '>', 0)->orderBy('debit', 'desc')->get();
        return view('users.debit_list', compact('debtors'));
    }

    public function clearDebit(User $user)
    {
        $this->authorize('clearDebit', $user);

        if ($user->debit <= 0) {
            return redirect()->back()->with('error', 'Este usuário não possui débitos pendentes.');
        }

        $user->update(['debit' => 0]);
        return redirect()->back()->with('success', 'Débito do usuário ' . $user->name . ' zerado com sucesso!');
    }
}
