@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4 d-flex justify-content-between align-items-center">
            <span>Utenti</span>
            <a href="{{ route('admin.users.create') }}" class="btn btn-success btn-sm">+ Nuovo Utente</a>
        </h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
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
                        <td class="text-nowrap">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-outline-primary">
                                Visualizza
                            </a>

                            @if ($user->id !== auth()->id())
                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                    data-bs-target="#deleteUserModal{{ $user->id }}">
                                    Elimina
                                </button>
                            @endif
                        </td>
                    </tr>

                    @if ($user->id !== auth()->id())
                        <!-- Modal -->
                        <div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1"
                            aria-labelledby="deleteUserModalLabel{{ $user->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteUserModalLabel{{ $user->id }}">Conferma eliminazione</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Chiudi"></button>
                                    </div>
                                    <div class="modal-body">
                                        Sei sicuro di voler eliminare <strong>{{ $user->first_name }}
                                            {{ $user->last_name }}</strong>?
                                        Questa azione è irreversibile.
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Conferma Elimina</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <tr>
                        <td colspan="4">Nessun utente trovato.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection