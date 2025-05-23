<!-- Modal -->
<div class="modal fade" id="createClientModal" tabindex="-1" aria-labelledby="createClientModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.clients.store') }}" method="POST" class="modal-content" novalidate>
            @csrf
            @if ($errors->any() && old('_from_modal'))
                <div class="alert alert-danger m-3">
                    <strong>Attenzione!</strong> Correggi gli errori sotto.
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li class="small">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <input type="hidden" name="_from_modal" value="1">

            <div class="modal-header">
                <h5 class="modal-title" id="createClientModalLabel">Nuovo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="first_name" class="form-label">Nome</label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">Cognome</label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email (opzionale)</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Telefono (opzionale)</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>
                <div class="mb-3">
                    <label for="birth_date" class="form-label">Data di nascita (opzionale)</label>
                    <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="submit" class="btn btn-primary">Salva cliente</button>
            </div>
        </form>
    </div>
</div>