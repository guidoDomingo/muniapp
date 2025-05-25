@extends('muni')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Administración de Preguntas Frecuentes</h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFaqModal">
                        <i class="fas fa-plus"></i> Nueva Pregunta
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                    @endif

                    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createFaqModal">
                        <i class="fas fa-plus"></i> Nueva Pregunta Frecuente
                    </button>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Pregunta</th>
                                <th>Respuesta</th>
                                <th>Categoría</th>
                                <th>Orden</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($faqs as $faq)
                            <tr>
                                <td>{{ $faq->id }}</td>
                                <td>{{ $faq->pregunta }}</td>
                                <td>{{ Str::limit($faq->respuesta, 100) }}</td>
                                <td>{{ $faq->categoria ?: 'General' }}</td>
                                <td>{{ $faq->orden }}</td>
                                <td>
                                    <span class="badge {{ $faq->activo ? 'bg-success' : 'bg-danger' }}">
                                        {{ $faq->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info edit-faq" 
                                            data-id="{{ $faq->id }}"
                                            data-pregunta="{{ $faq->pregunta }}"
                                            data-respuesta="{{ $faq->respuesta }}"
                                            data-categoria="{{ $faq->categoria }}"
                                            data-orden="{{ $faq->orden }}"
                                            data-activo="{{ $faq->activo }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editFaqModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" action="{{ route('chatbot.destroy', $faq->id) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta pregunta?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No hay preguntas frecuentes registradas</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear nueva FAQ -->
<div class="modal fade" id="createFaqModal" tabindex="-1" aria-labelledby="createFaqModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createFaqModalLabel">Nueva Pregunta Frecuente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('chatbot.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="pregunta" class="form-label">Pregunta</label>
                        <input type="text" class="form-control" id="pregunta" name="pregunta" required>
                    </div>
                    <div class="mb-3">
                        <label for="respuesta" class="form-label">Respuesta</label>
                        <textarea class="form-control" id="respuesta" name="respuesta" rows="5" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categoria" class="form-label">Categoría</label>
                                <input type="text" class="form-control" id="categoria" name="categoria">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="orden" class="form-label">Orden</label>
                                <input type="number" class="form-control" id="orden" name="orden" value="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar FAQ -->
<div class="modal fade" id="editFaqModal" tabindex="-1" aria-labelledby="editFaqModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editFaqModalLabel">Editar Pregunta Frecuente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editFaqForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_pregunta" class="form-label">Pregunta</label>
                        <input type="text" class="form-control" id="edit_pregunta" name="pregunta" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_respuesta" class="form-label">Respuesta</label>
                        <textarea class="form-control" id="edit_respuesta" name="respuesta" rows="5" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_categoria" class="form-label">Categoría</label>
                                <input type="text" class="form-control" id="edit_categoria" name="categoria">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_orden" class="form-label">Orden</label>
                                <input type="number" class="form-control" id="edit_orden" name="orden">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_activo" class="form-label">Estado</label>
                                <select class="form-control" id="edit_activo" name="activo">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar modal de edición
        const editButtons = document.querySelectorAll('.edit-faq');
        
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const pregunta = this.getAttribute('data-pregunta');
                const respuesta = this.getAttribute('data-respuesta');
                const categoria = this.getAttribute('data-categoria');
                const orden = this.getAttribute('data-orden');
                const activo = this.getAttribute('data-activo');
                
                document.getElementById('edit_pregunta').value = pregunta;
                document.getElementById('edit_respuesta').value = respuesta;
                document.getElementById('edit_categoria').value = categoria;
                document.getElementById('edit_orden').value = orden;
                document.getElementById('edit_activo').value = activo;
                
                document.getElementById('editFaqForm').action = '{{ url("chatbot") }}/' + id;
            });
        });
    });
</script>
@endsection
