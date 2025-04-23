@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Cambia Operatore</h2>

        <p>Seleziona l'utente con cui vuoi effettuare il login.</p>

        <ul class="list-group">
            @foreach ($users as $user)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>{{ $user->first_name }} {{ $user->last_name }}</span>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#switchUserModal"
                        data-user="{{ $user->id }}" data-name="{{ $user->first_name }} {{ $user->last_name }}">
                        Seleziona
                    </button>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="switchUserModal" tabindex="-1" aria-labelledby="switchUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.switch-user.switch') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="switchUserModalLabel">Conferma Cambio Profilo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="modalUserId">
                    <p id="modalUserName"></p>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                        @error('password')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Conferma</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            const modal = document.getElementById('switchUserModal');
            modal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const userId = button.getAttribute('data-user');
                const userName = button.getAttribute('data-name');

                document.getElementById('modalUserId').value = userId;
                document.getElementById('modalUserName').textContent = `Utente selezionato: ${userName}`;
            });
        </script>
    @endpush
@endsection