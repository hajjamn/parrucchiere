@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h2 class="fs-4 text-secondary my-4 text-white">{{ __('Profilo') }}</h2>

        {{-- Update Profile Information --}}
        <div class="card p-4 mb-4 bg-white shadow rounded-lg">
            <h2 class="text-secondary">{{ __('Informazioni Profilo') }}</h2>
            <p class="mt-1 text-muted">
                {{ __("Aggiorna le informazioni del tuo profilo e il tuo indirizzo email.") }}
            </p>

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('patch')

                <div class="mb-2">
                    <label for="first_name">Nome</label>
                    <input class="form-control @error('first_name') is-invalid @enderror" type="text" name="first_name"
                        id="first_name" value="{{ old('first_name', $user->first_name) }}" required autofocus>
                    @error('first_name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-2">
                    <label for="last_name">Cognome</label>
                    <input class="form-control @error('last_name') is-invalid @enderror" type="text" name="last_name"
                        id="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                    @error('last_name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-2">
                    <label for="email">Email</label>
                    <input class="form-control @error('email') is-invalid @enderror" type="email" name="email" id="email"
                        value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="d-flex align-items-center gap-4">
                    <button class="btn btn-primary" type="submit">{{ __('Salva') }}</button>
                    @if (session('status') === 'profile-updated')
                        <p class="fs-5 text-muted mb-0">{{ __('Salvato.') }}</p>
                    @endif
                </div>
            </form>
        </div>

        {{-- Update Password --}}
        <div class="card p-4 mb-4 bg-white shadow rounded-lg">
            <h2 class="text-secondary">{{ __('Aggiorna la Password') }}</h2>
            <p class="mt-1 text-muted">{{ __('Assicurati di usare una password lunga e sicura.') }}</p>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')

                <div class="mb-2">
                    <label for="current_password">Password attuale</label>
                    <input class="form-control @error('current_password') is-invalid @enderror" type="password"
                        name="current_password" id="current_password" autocomplete="current-password">
                    @error('current_password')
                        <span class="invalid-feedback mt-2 d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-2">
                    <label for="password">Nuova Password</label>
                    <input class="form-control @error('password') is-invalid @enderror" type="password" name="password"
                        id="password" autocomplete="new-password">
                    @error('password')
                        <span class="invalid-feedback mt-2 d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-2">
                    <label for="password_confirmation">Conferma Nuova Password</label>
                    <input class="form-control @error('password_confirmation') is-invalid @enderror" type="password"
                        name="password_confirmation" id="password_confirmation" autocomplete="new-password">
                    @error('password_confirmation')
                        <span class="invalid-feedback mt-2 d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="d-flex align-items-center gap-4">
                    <button class="btn btn-primary" type="submit">{{ __('Aggiorna Password') }}</button>
                    @if (session('status') === 'password-updated')
                        <p class="fs-5 text-muted mb-0">{{ __('Password aggiornata.') }}</p>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection