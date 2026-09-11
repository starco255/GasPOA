@extends('layouts.admin')

@section('title', 'Hariri Chapa')
@section('page-title', 'Hariri Chapa / Brand')

@section('content')

<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold">
                    <i class="bi bi-pencil me-2 text-primary"></i> Hariri Chapa
                </h5>
                <p class="text-muted">Badilisha maelezo ya chapa / brand.</p>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.categories.update', $category->id) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-tag me-1"></i> Jina la Chapa <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-lg" name="name" 
                               value="{{ old('name', $category->name) }}" required>
                        @error('name')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-pencil me-1"></i> Maelezo
                        </label>
                        <textarea class="form-control" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
                    </div>
                    
                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                               {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label">
                            <strong>Chapa iko active?</strong>
                        </label>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.products.index', '#categories') }}" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-x-circle me-1"></i> Ghairi
                        </a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-check-lg me-2"></i> Hifadhi Mabadiliko
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection