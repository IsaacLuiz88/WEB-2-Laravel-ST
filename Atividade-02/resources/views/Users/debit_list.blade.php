{{-- resources/views/users/debit_list.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Usuários com Débitos Pendentes</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if($debtors->isEmpty())
        <p class="alert alert-info">Nenhum usuário com débitos pendentes no momento.</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome do Usuário</th>
                    <th>Email</th>
                    <th>Débito Pendente</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($debtors as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>
                            <a href="{{ route('users.show', $user->id) }}">
                                {{ $user->name }}
                            </a>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>R$ {{ number_format($user->debit, 2, ',', '.') }}</td>
                        <td>
                            {{-- Botão para zerar o débito, visível apenas se tiver permissão --}}
                            @can('clearDebit', $user)
                                <form action="{{ route('users.clear_debit', $user->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja zerar o débito de {{ $user->name }}?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-primary btn-sm">Zerar Débito</button>
                                </form>
                            @else
                                <span class="text-muted">Sem permissão</span>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <a href="{{ route('home') }}" class="btn btn-secondary mt-3">Voltar</a>
</div>
@endsection