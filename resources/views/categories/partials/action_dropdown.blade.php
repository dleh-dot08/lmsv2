<div class="dropdown">
    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
        <i class="bx bx-dots-vertical-rounded"></i>
    </button>
    <div class="dropdown-menu">
        <a class="dropdown-item" href="{{ route('categories.edit', $category->id) }}">
            <i class="bx bx-edit-alt me-1"></i> Edit
        </a>
        
        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ $category->name }}? Sub-kategori (jika ada) akan menjadi kategori induk.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="dropdown-item text-danger">
                <i class="bx bx-trash me-1"></i> Hapus
            </button>
        </form>
    </div>
</div>