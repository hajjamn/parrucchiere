@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4">Utenti</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Ruolo</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td>
                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-outline-primary">
                                Visualizza
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Nessun utente trovato.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection