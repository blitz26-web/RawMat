@extends('layouts.app')

@section('title', 'Buat Batch Baru (BOM)')

@section('content')
<div class="mb-4">
    <a href="{{ route('batches.index') }}" class="text-decoration-none text-muted">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Batch
    </a>
    <h3 class="fw-bold mt-2"><i class="bi bi-plus-square me-2"></i>Buat Batch Produksi Baru</h3>
</div>

<form action="{{ route('batches.store') }}" method="POST">
    @csrf
    <div class="row g-4">
        <!-- Header Batch -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header fw-bold py-3">Header Batch</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nomor Batch <span class="text-danger">*</span></label>
                        <input type="text" name="batch_number" class="form-control @error('batch_number') is-invalid @enderror" value="{{ old('batch_number', 'BATCH-'.date('Ymd').'-'.rand(100,999)) }}" required>
                        @error('batch_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Dropdown Pilih Produk (BOM Master) -->
                    <div class="mb-3">
                        <label class="form-label">Pilih Produk Hasil (BOM) <span class="text-danger">*</span></label>
                        <select name="product_id" id="product_id_select" class="form-select @error('product_id') is-invalid @enderror">
                            <option value="">-- Pilih Produk Master --</option>
                            @foreach ($products as $prod)
                                <option value="{{ $prod->id }}" data-name="{{ $prod->name }}">
                                    {{ $prod->name }} ({{$prod->code }})
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="product_name" id="product_name_hidden">
                        <span class="text-muted extra-small d-block mt-1" style="font-size: 0.75rem;">
                            Memilih produk akan memuat resep bahan baku otomatis.
                        </span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Target Produk Jadi (Qty) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="target_qty" id="target_qty_input" class="form-control @error('target_qty') is-invalid @enderror" value="{{ old('target_qty', 100) }}" required>
                        @error('target_qty') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Dynamic Row Penggunaan Bahan Baku -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center py-3">
                    <div>
                        <span class="fw-bold">Rencana Penggunaan Bahan Baku</span>
                        <span id="bom-badge" class="badge bg-info-subtle text-info border ms-2 d-none">Resep BOM Terisi</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-success" id="add-material-btn">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Manual
                    </button>
                </div>
                <div class="card-body">
                    @error('materials')
                        <div class="alert alert-danger p-2 small mb-3">{{ $message }}</div>
                    @enderror

                    <div id="material-rows-container">
                        <!-- Baris pertama sebagai template awal -->
                        <div class="row g-2 mb-2 material-row">
                            <div class="col-md-7">
                                <select name="materials[0][material_id]" class="form-select material-select" required>
                                    <option value="">-- Pilih Bahan Baku --</option>
                                    @foreach ($materials as $mat)
                                        <option value="{{ $mat->id }}" data-stock="{{ $mat->current_stock }}" data-unit="{{ $mat->unit }}">
                                            {{ $mat->name }} ({{$mat->code }}) - Stok: {{ $mat->current_stock }} {{$mat->unit }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="number" step="0.0001" name="materials[0][qty_planned]" class="form-control qty-planned-input" placeholder="Plan Qty" required>
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-outline-danger btn-remove-row" disabled><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Simpan Batch (Draft)</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentBomData = [];
    const productSelect = document.getElementById('product_id_select');
    const productNameHidden = document.getElementById('product_name_hidden');
    const targetQtyInput = document.getElementById('target_qty_input');
    const container = document.getElementById('material-rows-container');
    const addBtn = document.getElementById('add-material-btn');
    const bomBadge = document.getElementById('bom-badge');

    // Mengoper data materials dari Blade ke JS via JSON
    const allMaterials = @json($materials);

    function buildMaterialOptionsHTML() {
        let options = '<option value="">-- Pilih Bahan Baku --</option>';
        allMaterials.forEach(mat => {
            options += `<option value="${mat.id}">${mat.name} (${mat.code}) - Stok: ${mat.current_stock} ${mat.unit}</option>`;
        });
        return options;
    }

    // 1. AJAX Event saat Produk dipilih
    productSelect.addEventListener('change', function() {
        const productId = this.value;
        const selectedOption = this.options[this.selectedIndex];
        productNameHidden.value = selectedOption.dataset.name || '';

        if (!productId) {
            currentBomData = [];
            bomBadge.classList.add('d-none');
            return;
        }

        fetch(`/products/${productId}/bom`)
            .then(res => res.json())
            .then(data => {
                currentBomData = data.bom;
                if (currentBomData.length > 0) {
                    bomBadge.classList.remove('d-none');
                    renderBomRows();
                } else {
                    bomBadge.classList.add('d-none');
                    alert('Produk ini belum memiliki resep Bill of Materials (BOM).');
                }
            })
            .catch(err => console.error(err));
    });

    // 2. Event saat Target Qty Berubah
    targetQtyInput.addEventListener('input', function() {
        if (currentBomData.length > 0) {
            renderBomRows();
        }
    });

    // 3. Render Baris Material Berdasarkan Resep BOM & Target Qty
    function renderBomRows() {
        container.innerHTML = '';
        const targetQty = parseFloat(targetQtyInput.value) || 0;

        currentBomData.forEach((item, index) => {
            const plannedTotal = (item.quantity_required * targetQty).toFixed(2);
            
            const row = document.createElement('div');
            row.className = 'row g-2 mb-2 material-row';
            row.innerHTML = `
                <div class="col-md-7">
                    <select name="materials[${index}][material_id]" class="form-select material-select" required>
                        <option value="${item.material_id}" selected>
                            ${item.material_name} (${item.material_code}) - Stok: ${item.current_stock} ${item.unit}
                        </option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="number" step="0.01" name="materials[${index}][qty_planned]" class="form-control qty-planned-input" value="${plannedTotal}" required>
                </div>
                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button>
                </div>
            `;

            row.querySelector('.btn-remove-row').addEventListener('click', function() {
                row.remove();
            });

            container.appendChild(row);
        });
    }

    // 4. Tambah Baris Manual
    addBtn.addEventListener('click', function() {
        const rowCount = container.querySelectorAll('.material-row').length;
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 material-row';
        row.innerHTML = `
            <div class="col-md-7">
                <select name="materials[${rowCount}][material_id]" class="form-select material-select" required>
                    ${buildMaterialOptionsHTML()}
                </select>
            </div>
            <div class="col-md-4">
                <input type="number" step="0.01" name="materials[${rowCount}][qty_planned]" class="form-control qty-planned-input" placeholder="Plan Qty" required>
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button>
            </div>
        `;

        row.querySelector('.btn-remove-row').addEventListener('click', function() {
            row.remove();
        });

        container.appendChild(row);
    });
});
</script>
@endpush