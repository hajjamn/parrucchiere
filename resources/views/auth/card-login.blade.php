@extends('layouts.app')

@section('content')
    <div class="container py-5 d-flex justify-content-center align-items-center" style="min-height: 70vh;">
        <div class="col-md-6">
            <h2 class="text-white text-center mb-4">Benvenuto, {{ $user->first_name }} {{ $user->last_name }}</h2>

            <div class="card shadow rounded-3">
                <div class="card-body">
                    <form method="POST" action="{{ route('card.login.attempt', $user->id) }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Inserisci la tua password</label>
                            <input type="password" name="password" class="form-control" required autofocus>
                            @error('password')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Accedi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection