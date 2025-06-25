@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Lista de Usuários</h1>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <a href="{{ route('users.show', $user) }}" class="btn btn-info btn-sm">
                            <i class="bi bi-eye"></i> Visualizar
                        </a>
                        @can('update', $user)
                        {{-- Verifica se o usuário logado pode editar este $user --}}
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        @endcan
                        
                        @can('updateRole', $user) {{-- Verifica se o usuário logado pode editar o papel deste $user --}}
                        <a href="{{ route('users.edit_role', $user->id) }}" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil-square"></i> Editar Papel
                        </a>
                        @endcan
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $users->links() }}
    </div>
</div>
@endsection