@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4">Aggiungi Nuovo Cliente</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Attenzione!</strong> Correggi gli errori sotto.
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li class="small">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.clients.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="first_name" class="form-label">Nome</label>
                <input type="text" name="first_name" class="form-control" required value="{{ old('first_name') }}">
            </div>

            <div class="mb-3">
                <label for="last_name" class="form-label">Cognome</label>
                <input type="text" name="last_name" class="form-control" required value="{{ old('last_name') }}">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email (opzionale)</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Telefono</label>
                <input type="text" name="phone" class="form-control" required value="{{ old('phone') }}">
            </div>

            <div class="mb-3">
                <label for="birth_date" class="form-label">Data di nascita (opzionale)</label>
                <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}">
            </div>

            <button type="submit" class="btn btn-primary">Salva</button>
            <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">Annulla</a>
        </form>
    </div>
@endsection