@extends('layouts.admin.app')

@push('meta')
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Variant Attribute - Lion POS</title>
@endpush

@section('content')
<div class="content">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <div class="page-title">
            <h4 class="fw-bold">Add Variant Attribute</h4>
            <h6>Create variant types (like size, color, material) for products</h6>
        </div>

        <div class="page-btn">
            <a href="{{ route('admin.variant-attributes.index') }}" class="btn btn-primary">
                <i class="ti ti-list me-1"></i> Variant List
            </a>
        </div>
    </div>

    <div class="card shadow-sm">

        {{-- GLOBAL ERROR ALERT --}}
        @if ($errors->any())
            <div class="alert alert-danger rounded-3 m-3">
                <strong>Please fix the following errors:</strong>
                <ul class="mt-2 mb-1">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.variant-attributes.store') }}" method="POST">
            @csrf

            <div class="card-body">
                <div class="row">

                    <!-- Variant Name -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">
                            Variant Name <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-tag"></i></span>
                            <input type="text"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="Size, Color, Material"
                                   value="{{ old('name') }}"
                                   required>
                        </div>

                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">
                            Slug <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-link"></i></span>
                            <input type="text"
                                   name="slug"
                                   class="form-control @error('slug') is-invalid @enderror"
                                   placeholder="auto-generated"
                                   value="{{ old('slug') }}"
                                   required>
                        </div>

                        @error('slug')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Variant Type -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">
                            Variant Type <span class="text-danger">*</span>
                        </label>

                        <select name="type"
                                class="form-select @error('type') is-invalid @enderror"
                                required>
                            <option value="">-- Select Type --</option>
                            <option value="text"    {{ old('type') === 'text' ? 'selected' : '' }}>Text</option>
                            <option value="color"   {{ old('type') === 'color' ? 'selected' : '' }}>Color</option>
                            <option value="size"    {{ old('type') === 'size' ? 'selected' : '' }}>Size</option>
                            <option value="material"{{ old('type') === 'material' ? 'selected' : '' }}>Material</option>
                        </select>

                        @error('type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Sort Order -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">Sort Order</label>
                        <input type="number"
                               name="sort_order"
                               class="form-control"
                               placeholder="1, 2, 3..."
                               value="{{ old('sort_order') }}">
                    </div>

                    <!-- Variant Values -->
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-semibold">
                            Variant Values <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               id="variant_values"
                               class="form-control @error('values') is-invalid @enderror"
                               name="values"
                               placeholder="Enter values and press Enter (e.g. Red, Blue, Green)"
                               value="{{ old('values') }}">

                        <span class="text-muted small">Values will appear as tags below</span>

                        <div id="tag-container" class="d-flex flex-wrap gap-2 mt-2"></div>

                        @error('values')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- COLOR PICKER (IF TYPE=COLOR) -->
                    <div class="col-md-12 mb-4 d-none" id="color-picker-section">
                        <label class="form-label fw-semibold">Color Preview</label>
                        <input type="color" class="form-control form-control-color">
                    </div>

                    <!-- Description -->
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-semibold">Description (optional)</label>

                        <textarea name="description"
                                  rows="3"
                                  class="form-control">{{ old('description') }}</textarea>
                    </div>

                    <!-- Status -->
                    <div class="col-md-12 mb-4">
                        <div class="status-toggle d-flex justify-content-between align-items-center p-3 bg-light rounded">
                            <span class="fw-semibold">Status</span>

                            <input type="hidden" name="status" value="0">
                            <input type="checkbox" name="status" value="1"
                                   class="check"
                                   id="variantStatus"
                                   {{ old('status', 1) == 1 ? 'checked' : '' }}>

                            <label for="variantStatus" class="checktoggle"></label>
                        </div>

                        @error('status')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- SUBMIT FOOTER -->
            <div class="card-footer d-flex justify-content-end">
                <a href="{{ route('admin.variant-attributes.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Variant</button>
            </div>

        </form>

    </div>

</div>
@endsection

@push('scripts')
<script>
// Auto slug generation
document.querySelector('input[name="name"]').addEventListener('keyup', function () {
    document.querySelector('input[name="slug"]').value =
        this.value.toLowerCase().replace(/ /g, "-").replace(/[^\w-]+/g, "");
});

// Show color picker only if "color" type is selected
document.querySelector('select[name="type"]').addEventListener('change', function () {
    document.getElementById('color-picker-section').classList.toggle('d-none', this.value !== 'color');
});

// Tag Input System
const input = document.getElementById("variant_values");
const tagContainer = document.getElementById("tag-container");

input.addEventListener("keyup", function (e) {
    if (e.key === "Enter" && this.value.trim() !== "") {
        let val = this.value.trim();
        createTag(val);
        this.value = "";
        updateHiddenValues();
    }
});

// Create a tag component
function createTag(value) {
    let tag = document.createElement("span");
    tag.className = "badge bg-primary p-2";
    tag.innerHTML = value + ' <span class="ms-2 text-white remove-tag" style="cursor:pointer;">×</span>';
    tagContainer.appendChild(tag);
}

// Remove tag
document.addEventListener("click", function (e) {
    if (e.target.classList.contains("remove-tag")) {
        e.target.parentElement.remove();
        updateHiddenValues();
    }
});

// Save tags into input field
function updateHiddenValues() {
    let tags = [...document.querySelectorAll("#tag-container span")]
        .map(tag => tag.textContent.trim().slice(0, -1));
    input.value = tags.join(",");
}
</script>
@endpush
