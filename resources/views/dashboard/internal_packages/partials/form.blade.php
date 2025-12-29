@include('dashboard.errors')

<div class="row">
    <div class="col-md-6">
        {{ BsForm::text('name')
            ->label(trans('internal_packages.attributes.name'))
            ->required()
        }}
    </div>
    <div class="col-md-3">
        {{ BsForm::number('quantity')
            ->label(trans('internal_packages.attributes.quantity'))
            ->min(1)
            ->value(old('quantity', $internal_package->quantity ?? 1))
        }}
    </div>
    <div class="col-md-3">
        {{ BsForm::number('weight')
            ->label(trans('internal_packages.attributes.weight'))
            ->attribute('step', '0.01')
            ->min(0)
            ->placeholder('الوزن بالكيلوجرام')
        }}
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        {{ BsForm::textarea('description')
            ->label(trans('internal_packages.attributes.description'))
            ->rows(2)
        }}
    </div>
</div>

<hr>

<!-- محتويات الطرد -->
<h4 class="mt-3 mb-3"><i class="fas fa-cubes"></i> محتويات الطرد (الأصناف)</h4>

<div id="contents_container">
    <div class="content-row border p-3 mb-3 rounded bg-light">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="contents_0_item_id">الصنف <span class="text-danger">*</span></label>
                    <select name="contents[0][item_id]" id="contents_0_item_id" class="form-control">
                        <option value="">-- اختر الصنف --</option>
                        @foreach(App\Models\Item::all() as $item)
                            <option value="{{ $item->id }}">{{ $item->name }} (المتوفر: {{ $item->quantity }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="contents_0_quantity">الكمية <span class="text-danger">*</span></label>
                    <input type="number" name="contents[0][quantity]" id="contents_0_quantity" class="form-control" min="1" value="1">
                </div>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="form-group w-100">
                    <button type="button" class="btn btn-danger btn-block" onclick="removeContentRow(this)">
                        <i class="fas fa-trash"></i> حذف
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<button type="button" class="btn btn-success mb-3" onclick="addContentRow()">
    <i class="fas fa-plus"></i> إضافة صنف
</button>

@push('scripts')
<script>
    let contentIndex = 1;
    const availableItems = @json(App\Models\Item::select('id', 'name', 'quantity')->get());
    
    function addContentRow() {
        const container = document.getElementById('contents_container');
        
        let itemOptions = '<option value="">-- اختر الصنف --</option>';
        availableItems.forEach(item => {
            itemOptions += `<option value="${item.id}">${item.name} (المتوفر: ${item.quantity})</option>`;
        });
        
        const newRow = `
            <div class="content-row border p-3 mb-3 rounded bg-light">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="contents_${contentIndex}_item_id">الصنف <span class="text-danger">*</span></label>
                            <select name="contents[${contentIndex}][item_id]" id="contents_${contentIndex}_item_id" class="form-control">
                                ${itemOptions}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="contents_${contentIndex}_quantity">الكمية <span class="text-danger">*</span></label>
                            <input type="number" name="contents[${contentIndex}][quantity]" id="contents_${contentIndex}_quantity" class="form-control" min="1" value="1">
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button type="button" class="btn btn-danger btn-block" onclick="removeContentRow(this)">
                                <i class="fas fa-trash"></i> حذف
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newRow);
        contentIndex++;
    }
    
    function removeContentRow(button) {
        const container = document.getElementById('contents_container');
        const rows = container.getElementsByClassName('content-row');
        
        if (rows.length > 1) {
            button.closest('.content-row').remove();
        } else {
            alert('يجب أن يكون هناك صنف واحد على الأقل');
        }
    }
</script>
@endpush
