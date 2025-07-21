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
            {{-- Informação de disponibilidade do livro visível para todos --}}
            <p>
                <strong>Status:</strong>
                @if($book->isBorrowed())
                    <span class="badge bg-warning">Atualmente Emprestado</span>
                @else
                    <span class="badge bg-success">Disponível</span>
                @endif
            </p>
        </div>
    </div>

    @auth
        @can('create', App\Models\Borrowing::class)
        <div class="card mb-4 mt-4">
            <div class="card-header">Registrar Empréstimo</div>
            <div class="card-body">
                @if(!$book->isBorrowed())
                    <form action="{{ route('books.borrow', $book) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Usuário</label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="" selected>Selecione um usuário</option>
                                {{-- É crucial que a variável $users seja passada do controller para esta view --}}
                                @foreach($users as $userOption)
                                    <option value="{{ $userOption->id }}">{{ $userOption->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Registrar Empréstimo</button>
                    </form>
                @else
                    <div class="alert alert-warning">
                        Este livro está atualmente emprestado.
                        @php
                            $currentBorrowing = $book->currentBorrowing();
                        @endphp
                        @if($currentBorrowing)
                            <p class="mb-0">
                                Emprestado por: <strong>{{ $currentBorrowing->user->name ?? 'Usuário Desconhecido' }}</strong>
                                em <strong>{{ $currentBorrowing->borrowed_at->format('d/m/Y H:i') }}</strong>
                                @if($currentBorrowing->getDaysLate() > 0)
                                    {{-- A multa aqui é exibida para quem pode criar empréstimos (bibliotecários/admins) --}}
                                    <span class="badge bg-danger ms-2">Atrasado: {{ $currentBorrowing->getDaysLate() }} dias (Multa: R$ {{ number_format($currentBorrowing->calculateFine(), 2, ',', '.') }})</span>
                                @else
                                    <span class="badge bg-success ms-2">Em dia</span>
                                @endif
                            </p>
                            {{-- Botão para marcar como devolvido, visível apenas para quem pode atualizar o empréstimo --}}
                            @can('update', $currentBorrowing)
                                <form action="{{ route('borrowings.return', $currentBorrowing->id) }}" method="POST" class="mt-2">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-info btn-sm">Marcar como Devolvido</button>
                                </form>
                            @endcan
                        @endif
                    </div>
                @endif
            </div>
        </div>
        @endcan {{-- Fim do @can('create', App\Models\Borrowing::class) para Registrar Empréstimo --}}

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
                                <th>Status de Atraso</th>
                                {{-- Apenas exibe a coluna "Ações" se o usuário logado puder "update" (devolver) um empréstimo --}}
                                @can('update', \App\Models\Borrowing::class) {{-- Pode ser um Gate ou Policy para a classe Borrowing --}}
                                    <th>Ações</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($book->users as $user)
                                @php
                                    // Como $book->users é um many-to-many com pivot, $user->pivot é o modelo Borrowing
                                    $borrowing = $user->pivot;
                                    $getDaysLate = $borrowing->getDaysLate();
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('users.show', $user->id) }}">
                                            {{ $user->name }}
                                        </a>
                                    </td>
                                    <td>{{ $borrowing->borrowed_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($borrowing->returned_at)
                                            {{ $borrowing->returned_at->format('d/m/Y H:i') }}
                                        @else
                                            Em Aberto
                                        @endif
                                    </td>
                                    <td>
                                        @if($getDaysLate > 0)
                                            <span class="badge bg-danger">
                                                Atrasado: {{ $getDaysLate }} dia{{ $getDaysLate > 1 ? 's' : '' }}
                                                {{-- Só mostra a multa se o usuário tiver permissão para 'update' ou 'view fines' (se você tiver uma policy específica) --}}
                                                @can('update', $borrowing) {{-- Ou uma policy específica para ver multas, ex: @can('viewFine', $borrowing) --}}
                                                    @if($borrowing->returned_at !== null)
                                                        (Multa: R$ {{ number_format($borrowing->calculateFine(), 2, ',', '.') }})
                                                    @endif
                                                @endcan
                                            </span>
                                        @else
                                            <span class="badge bg-success">Em dia</span>
                                        @endif
                                    </td>
                                    {{-- Apenas exibe a célula "Ações" se o usuário logado puder "update" (devolver) um empréstimo --}}
                                    @can('update', \App\Models\Borrowing::class)
                                        <td>
                                            @if(is_null($borrowing->returned_at))
                                                {{-- Botão "Devolver" visível apenas se o empréstimo ainda está em aberto --}}
                                                {{-- E se o usuário logado tiver permissão para 'update' este empréstimo --}}
                                                @can('update', $borrowing)
                                                    <form action="{{ route('borrowings.return', $borrowing->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button class="btn btn-warning btn-sm">Devolver</button>
                                                    </form>
                                                @endcan
                                            @endif
                                        </td>
                                    @endcan
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    @else {{-- Conteúdo para usuários NÃO logados --}}
        <div class="card mb-4 mt-4">
            <div class="card-header">Ações do Livro</div>
            <div class="card-body">
                <p class="alert alert-info">Faça login para registrar ou gerenciar empréstimos e ver o histórico completo.</p>
            </div>
        </div>
    @endauth {{-- Fim do bloco @auth principal --}}

    <a href="{{ route('books.index') }}" class="btn btn-secondary mt-3">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
</div>
@endsection