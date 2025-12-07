@extends('layouts.admin.app')

@push('meta')
<title>Edit Variant Attribute - Lion POS</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@endpush

@section('content')
<div class="content">

    <div class="page-header">
        <div class="page-title">
            <h4 class="fw-bold">Edit Variant Attribute</h4>
            <h6>Update variant name, type, and values</h6>
        </div>

        <div class="page-btn">
            <a href="{{ route('admin.variant-attributes.index') }}" class="btn btn-primary">
                <i class="ti ti-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <div class="card shadow-sm">

        {{-- GLOBAL VALIDATION ERROR --}}
        @if ($errors->any())
            <div class="alert alert-danger rounded-3 m-3">
                <strong>Please fix the errors:</strong>
                <ul class="mt-2 mb-2">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <form action="{{ route('admin.variant-attributes.update', $variant->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body">
                <div class="row">

                    <!-- Variant Name -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">Variant Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-tag"></i></span>
                            <input type="text"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $variant->name) }}"
                                   required>
                        </div>
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">Slug <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-link"></i></span>
                            <input type="text"
                                   name="slug"
                                   class="form-control @error('slug') is-invalid @enderror"
                                   value="{{ old('slug', $variant->slug) }}"
                                   required>
                        </div>
                        @error('slug')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Variant Type -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                        <select name="type"
                                class="form-select @error('type') is-invalid @enderror">
                            <option value="">-- Select Type --</option>
                            @foreach(\App\Models\VariantAttribute::TYPES as $type)
                                <option value="{{ $type }}"
                                    {{ old('type', $variant->type) == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>

                        @error('type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Sort Order -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">Sort Order</label>
                        <input type="number"
                               class="form-control"
                               name="sort_order"
                               value="{{ old('sort_order', $variant->sort_order) }}">
                    </div>

                    <!-- Variant Values (Tags Input) -->
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-semibold">Values <span class="text-danger">*</span></label>

                        <input type="text"
                               id="variant_values"
                               name="values"
                               class="form-control @error('values') is-invalid @enderror"
                               placeholder="Enter value & press Enter"
                               value="{{ old('values', implode(',', $variant->values ?? [])) }}">

                        <div id="tag-container" class="d-flex flex-wrap gap-2 mt-2"></div>

                        @error('values')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Color picker if type == color -->
                    <div class="col-md-12 mb-4 {{ old('type', $variant->type) == 'color' ? '' : 'd-none' }}"
                         id="color-picker-section">
                        <label class="form-label fw-semibold">Color Preview</label>
                        <input type="color" class="form-control form-control-color" value="#ff0000">
                    </div>

                    <!-- Description -->
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-semibold">Description (optional)</label>
                        <textarea name="description"
                                  class="form-control"
                                  rows="3">{{ old('description', $variant->description) }}</textarea>
                    </div>

                    <!-- Status -->
                    <div class="col-md-12">
                        <div class="status-toggle d-flex justify-content-between align-items-center p-3 bg-light rounded">
                            <span class="fw-semibold">Status</span>

                            <input type="hidden" name="status" value="0">
                            <input type="checkbox" name="status" value="1"
                                   id="variantStatus"
                                   class="check"
                                   {{ old('status', $variant->status) == 1 ? 'checked' : '' }}>
                            <label for="variantStatus" class="checktoggle"></label>
                        </div>

                        @error('status')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                </div> <!-- row end -->
            </div>

            <!-- Footer -->
            <div class="card-footer d-flex justify-content-end">
                <a href="{{ route('admin.variant-attributes.index') }}" class="btn btn-secondary me-2">
                    Cancel
                </a>

                <button type="submit" class="btn btn-primary">
                    Update Variant
                </button>
            </div>

        </form>

    </div>

</div>
@endsection

@push('scripts')
<script>
// Auto Slug
document.querySelector('input[name="name"]').addEventListener('keyup', function () {
    document.querySelector('input[name="slug"]').value =
        this.value.toLowerCase().replace(/ /g, "-").replace(/[^\w-]+/g, "");
});

// Show color picker for type=color
const typeSelect = document.querySelector('select[name="type"]');
typeSelect.addEventListener('change', function () {
    document.getElementById('color-picker-section')
        .classList.toggle('d-none', this.value !== 'color');
});

// TAG INPUT SYSTEM
const input = document.getElementById("variant_values");
const tagContainer = document.getElementById("tag-container");

// Load initial tags from value
(function loadTags() {
    let values = input.value.split(',').map(v => v.trim()).filter(v => v);
    values.forEach(v => createTag(v));
})();

// Add tag on Enter key
input.addEventListener("keyup", function (e) {
    if (e.key === "Enter" && this.value.trim() !== "") {
        createTag(this.value.trim());
        this.value = "";
        updateHiddenValues();
    }
});

// Create tag
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

// Update hidden input values
function updateHiddenValues() {
    let tags = [...document.querySelectorAll("#tag-container span")]
        .map(tag => tag.textContent.trim().slice(0, -1));
    input.value = tags.join(",");
}
</script>
@endpush
