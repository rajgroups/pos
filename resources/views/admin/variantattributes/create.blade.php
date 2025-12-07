@extends('layouts.admin.app')

@push('meta')
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Variant Attribute - Lion POS</title>
@endpush

@section('content')
<div class="content">

    <div class="page-header">
        <div class="page-title">
            <h4 class="fw-bold">Add Variant Attribute</h4>
            <h6>Create variant types (size, color, material, etc.)</h6>
        </div>

        <div class="page-btn">
            <a href="{{ route('admin.variant-attributes.index') }}" class="btn btn-primary">
                <i class="ti ti-list me-1"></i> Variant List
            </a>
        </div>
    </div>

    <div class="card shadow-sm">

        <form action="{{ route('admin.variant-attributes.store') }}" method="POST">
            @csrf

            <div class="card-body">

                <div class="row">

                    <!-- Variant Name -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">Variant Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-tag"></i></span>
                            <input type="text" name="name"
                                   class="form-control"
                                   placeholder="Size, Color, Material"
                                   required>
                        </div>
                    </div>

                    <!-- Variant Slug -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">Slug <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-link"></i></span>
                            <input type="text" name="slug"
                                   class="form-control"
                                   placeholder="auto-generated"
                                   required>
                        </div>
                    </div>

                    <!-- Variant Type -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">Variant Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="">-- Select Type --</option>
                            <option value="text">Text</option>
                            <option value="color">Color</option>
                            <option value="size">Size</option>
                            <option value="material">Material</option>
                        </select>
                    </div>

                    <!-- Sort Order -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" placeholder="1, 2, 3...">
                    </div>

                    <!-- Values Tag Input -->
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-semibold">Variant Values <span class="text-danger">*</span></label>
                        <input type="text" id="variant_values"
                               class="form-control"
                               name="values"
                               placeholder="Enter values and press Enter (e.g. Red, Blue, Green)">
                        <span class="text-muted small">Values will be added as tags</span>

                        <!-- Dynamic tag container -->
                        <div id="tag-container" class="d-flex flex-wrap gap-2 mt-2"></div>
                    </div>

                    <!-- Optional Color Palette If Type = Color -->
                    <div class="col-md-12 mb-4 d-none" id="color-picker-section">
                        <label class="form-label fw-semibold">Color Preview</label>
                        <input type="color" class="form-control form-control-color" value="#ff0000">
                        <span class="small text-muted">For visual reference only.</span>
                    </div>

                    <!-- Description -->
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-semibold">Description (optional)</label>
                        <textarea name="description" rows="3" class="form-control"
                                  placeholder="Describe the purpose of this variant (ex: This attribute is used for shirt sizes)">
                        </textarea>
                    </div>

                    <!-- Status -->
                    <div class="col-md-12 mb-4">
                        <div class="status-toggle d-flex justify-content-between align-items-center p-3 bg-light rounded">
                            <span class="fw-semibold">Status</span>
                            <input type="hidden" name="status" value="0">
                            <input type="checkbox" name="status" value="1" class="check" checked id="variantStatus">
                            <label for="variantStatus" class="checktoggle"></label>
                        </div>
                    </div>

                </div> <!-- row end -->

            </div>

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
        let tag = document.createElement("span");
        tag.className = "badge bg-primary p-2";
        tag.innerHTML = val + ' <span class="ms-2 text-white remove-tag" style="cursor:pointer;">×</span>';

        tagContainer.appendChild(tag);
        this.value = "";

        // Store all tags into hidden field for backend
        updateHiddenValues();
    }
});

// Remove tag
document.addEventListener("click", function (e) {
    if (e.target.classList.contains("remove-tag")) {
        e.target.parentElement.remove();
        updateHiddenValues();
    }
});

function updateHiddenValues() {
    let tags = [...document.querySelectorAll("#tag-container span")].map(tag => tag.textContent.trim().slice(0, -1));
    input.value = tags.join(",");
}
</script>
@endpush
