@extends('layouts.app')

@section('content')
    <div class="container py-4 text-white">
        <h1 class="mb-4">Modifica Cliente</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Attenzione!</strong> Correggi gli errori sotto.
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.clients.update', $client->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="first_name" class="form-label">Nome</label>
                <input type="text" class="form-control" id="first_name" name="first_name"
                    value="{{ old('first_name', $client->first_name) }}">
            </div>

            <div class="mb-3">
                <label for="last_name" class="form-label">Cognome</label>
                <input type="text" class="form-control" id="last_name" name="last_name"
                    value="{{ old('last_name', $client->last_name) }}">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email (opzionale)</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $client->email) }}">
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Telefono (opzionale)</label>
                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $client->phone) }}">
            </div>

            <div class="mb-3">
                <label for="birth_date" class="form-label">Data di nascita (opzionale)</label>
                <input type="date" class="form-control" id="birth_date" name="birth_date"
                    value="{{ old('birth_date', $client->birth_date) }}">
            </div>

            <button type="submit" class="btn btn-primary">Salva</button>
            <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">Annulla</a>
        </form>
    </div>
@endsection