@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Detalhes do Livro</h1>

    {{-- Mensagens de sucesso e erro --}}
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

    <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/default-cover.png') }}" alt="Capa" width="150">
     
    <div class="card">
        <div class="card-header">
            <strong>Título:</strong> {{ $book->title }}
        </div>
        <div class="card-body">
            <p>
                <strong>Autor: </strong>
                @if($book->author)
                <a href="{{ route('authors.show', $book->author->id) }}">
                    {{ $book->author->name }}
                </a>
                @else
                    Author Unknown
                @endif
            </p>
            <p><strong>Editora:</strong>
                @if($book->publisher)
                <a href="{{ route('publishers.show', $book->publisher->id) }}">
                    {{ $book->publisher->name }}
                </a>
                @else
                    Publisher Unknown
                @endif
            </p>
            <p><strong>Categoria:</strong>
                @if($book->category)
                <a href="{{ route('categories.show', $book->category->id) }}">
                    {{ $book->category->name }}
                </a>
                @else
                    Category Unknown
                @endif
            </p>
        </div>
    </div>

    <!-- Formulário para Empréstimos -->
    <div class="card mb-4 mt-4">
        <div class="card-header">Registrar Empréstimo</div>
        <div class="card-body">
             {{-- Condição para mostrar o formulário de empréstimo:
                 - Usuário logado
                 - Livro não emprestado
                 - Usuário é bibliotecário ou admin (se a política permitir create em Borrowing) --}}
            @auth
                @if(!$book->isBorrowed())
                    @can('create', App\Models\Borrowing::class)
                    <form action="{{ route('books.borrow', $book) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Usuário</label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="" selected>Selecione um usuário</option>
                                @foreach($users as $userOption)
                                    <option value="{{ $userOption->id }}">{{ $userOption->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Registrar Empréstimo</button>
                    </form>
                @else
                    <p class="alert alert-info">Você não tem permissão para registrar empréstimos.</p>
                @endcan
                @else
                    {{-- Mensagem quando o livro está emprestado --}}
                    <div class="alert alert-warning">
                        Este livro está atualmente emprestado.
                        @php
                            $currentBorrowing = $book->currentBorrowing();
                        @endphp
                        @if($currentBorrowing)
                            <p class="mb-0">
                                Emprestado por: **{{ $currentBorrowing->user->name ?? 'Usuário Desconhecido' }}**
                                em **{{ $currentBorrowing->borrowed_at->format('d/m/Y H:i') }}**
                            </p>
                            {{-- Botão para marcar como devolvido, visível apenas para bibliotecário/admin --}}
                            @can('update', $currentBorrowing) {{-- Verifica se o usuário pode 'update' este empréstimo (para devolver) --}}
                                <form action="{{ route('borrowings.return', $currentBorrowing->id) }}" method="POST" class="mt-2">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-info btn-sm">Marcar como Devolvido</button>
                                </form>
                            @endcan
                        @endif
                    </div>
                @endif
            @else
                <p class="alert alert-info">Faça login para registrar ou gerenciar empréstimos.</p>
            @endauth
        </div>
    </div>

    <!-- Histórico de Empréstimos -->
    <div class="card mb-4">
        <div class="card-header">Histórico de Empréstimos</div>
        <div class="card-body">
            @if($book->users->isEmpty())
                <p>Nenhum empréstimo registrado.</p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>Data de Empréstimo</th>
                            <th>Data de Devolução</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($book->users as $user)
                            <tr>
                                <td>
                                    <a href="{{ route('users.show', $user->id) }}">
                                        {{ $user->name }}
                                    </a>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($user->pivot->borrowed_at)->format('d/m/Y H:i') }}</td>
                                <td>@if($user->pivot->returned_at)
                                    {{ \Carbon\Carbon::parse($user->pivot->returned_at)->format('d/m/Y H:i') }}
                                    @else
                                    <span class="text-muted">Não devolvido</span>
                                    @endif
                                <td>
                                    @if(is_null($user->pivot->returned_at))
                                        @can('update', Borrowing::find($user->pivot->id)) {{-- Verifica se o usuário pode 'update' este empréstimo --}}
                                            <form action="{{ route('borrowings.return', $user->pivot->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn btn-warning btn-sm">Devolver</button>
                                            </form>
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <a href="{{ route('books.index') }}" class="btn btn-secondary mt-3">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
</div>
@endsection