<li>
    <div class="d-flex align-items-center mb-2">
        @if($category->childrenRecursive->count() > 0)
            <a href="javascript:void(0);" class="me-2 text-primary text-decoration-none fs-16">
                <i class="ti ti-chevron-down tree-toggle"></i>
            </a>
        @else
            <span class="me-2 ms-3 d-inline-block" style="width: 16px;"></span>
        @endif
        
        <span class="badge {{ $category->is_active ? 'bg-success' : 'bg-danger' }} me-2">
            {{ $category->is_active ? 'Active' : 'Inactive' }}
        </span>
        
        <strong class="fs-14 text-dark">{{ $category->name }}</strong>
        <span class="text-muted ms-2 fs-12">({{ $category->service_mode ?? 'instant' }})</span>
        
        <div class="ms-auto">
            <a href="{{ route('admin.category.edit', $category->id) }}" class="btn btn-sm btn-light py-1 px-2">
                <i class="ti ti-edit"></i> Edit
            </a>
        </div>
    </div>
    
    @if($category->childrenRecursive->count() > 0)
        <ul class="list-unstyled ms-4 ps-3 border-start">
            @foreach($category->childrenRecursive as $child)
                @include('admin.category.partials.tree_item', ['category' => $child])
            @endforeach
        </ul>
    @endif
</li>
