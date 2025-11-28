@extends('layouts.admin.app')

@push('meta')
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Variant Attribute - Lion POS</title>
@endpush

@section('content')
<div class="content">

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Add Variant Attribute</h4>
                <h6>Modify variant and its values</h6>
            </div>
        </div>

        <div class="page-btn">
            <a href="{{ route('admin.variant-attributes.index') }}" class="btn btn-primary">
                <i class="ti ti-list me-1"></i> List of Variants
            </a>
        </div>
    </div>

    <div class="card">
        <form action="" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body">

                <div class="row">

                    <!-- Variant Name -->
                    <div class="col-md-6 mb-3">
                        <div class="mb-3">
							<label class="form-label">Variant<span class="text-danger ms-1">*</span></label>
							<input type="text" class="form-control">
						</div>
                    </div>

                    <!-- Status Switch -->
                    <div class="col-md-6 mb-3 ">
                        <div class="mb-3">
							<label class="form-label">Values<span class="text-danger ms-1">*</span></label>
							<input class="form-control" type="text"  name="specialist" value="">
							<span class="tag-text mt-2 d-flex">Enter value separated by comma</span>
						</div>
                    </div>

                    <div class="col-md-6 mb-3 ">
                        <div class="mb-0 mt-4">
								<div class="status-toggle modal-status d-flex justify-content-between align-items-center">
									<span class="status-label">Status</span>
									<input type="checkbox" id="user2" class="check" checked="">
									<label for="user2" class="checktoggle"></label>
								</div>
						</div>
					</div>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="card-footer d-flex justify-content-end">
                <a href="{{ route('admin.variant-attributes.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Variant</button>
            </div>

        </form>
    </div>

</div>
@endsection
