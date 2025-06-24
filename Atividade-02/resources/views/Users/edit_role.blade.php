@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Editar Papel do Usuário: {{ $user->name }}</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('users.update_role', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="role" class="form-label">Papel</label>
                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                        @foreach($roles as $roleOption)
                            <option value="{{ $roleOption }}" {{ $user->role === $roleOption ? 'selected' : '' }}>
                                {{ ucfirst($roleOption) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Salvar Papel</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection