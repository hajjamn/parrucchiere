@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4">Registra una nuova prestazione</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Attenzione!</strong> Correggi gli errori sotto.<br><br>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.service-logs.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="client_id" class="form-label">Cliente</label>
                <select name="client_id" id="client_id" class="form-select" required>
                    <option value="">-- Seleziona un cliente --</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->first_name }} {{ $client->last_name }}
                        </option>
                    @endforeach
                </select>

                <div class="mb-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#createClientModal">
                        + Aggiungi nuovo cliente
                    </button>
                </div>
            </div>

            <div class="mb-3">
                <label for="service_ids" class="form-label">Servizi</label>
                <select name="service_ids[]" id="service_ids" class="form-select" multiple required>
                    @foreach ($services as $service)
                        <option value="{{ $service->id }}" {{ in_array($service->id, old('service_ids', [])) ? 'selected' : '' }}>
                            {{ $service->name }} (€{{ number_format($service->price, 2, ',', '.') }})
                        </option>
                    @endforeach
                </select>
                <div class="form-text">Tieni premuto Ctrl (o Cmd su Mac) per selezionare più di un servizio.</div>
            </div>

            <div class="mb-3">
                <label for="performed_at" class="form-label">Data e ora</label>
                <input type="datetime-local" name="performed_at" id="performed_at" class="form-control"
                    value="{{ old('performed_at') }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Registra</button>
            <a href="{{ route('admin.service-logs.index') }}" class="btn btn-secondary">Annulla</a>
        </form>
    </div>

    <!-- Modal -->
<div class="modal fade" id="createClientModal" tabindex="-1" aria-labelledby="createClientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form action="{{ route('admin.clients.store') }}" method="POST" class="modal-content" novalidate>
        @csrf
        @if ($errors->any() && old('_from_modal'))
    <div class="alert alert-danger">
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
            <label for="phone" class="form-label">Telefono</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
          </div>
          <div class="mb-3">
            <label for="birth_date" class="form-label">Data di nascita</label>
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
  
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const newClientId = @json(session('new_client_id'));
        console.log('newClientId from session:', newClientId);

        if (newClientId) {
            const clientSelect = document.getElementById('client_id');
            if (clientSelect) {
                clientSelect.value = newClientId;
            }

            // Optional: Scroll to the dropdown to confirm selection
            clientSelect.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
</script>

@if ($errors->any() && old('_from_modal'))
    <script>
        const modal = new bootstrap.Modal(document.getElementById('createClientModal'));
        modal.show();
    </script>
@endif
@endpush