@include('dashboard.errors')

<div class="row">
    <div class="col-md-4">
        {{ BsForm::text('shipment_number')
            ->label(trans('outbound_shipments.attributes.shipment_number'))
            ->required()
            ->value(old('shipment_number', $outbound_shipment->shipment_number ?? 'OUT-' . date('YmdHis')))
        }}
    </div>
    <div class="col-md-4">
        {{ BsForm::select('project_id')
            ->label(trans('outbound_shipments.attributes.project_id'))
            ->options(App\Models\Project::pluck('name', 'id'))
            ->placeholder('-- اختر الكوبون --')
        }}
    </div>
    <div class="col-md-4">
        {{ BsForm::select('sub_warehouse_id')
            ->label(trans('outbound_shipments.attributes.sub_warehouse_id'))
            ->options(App\Models\SubWarehouse::pluck('name', 'id'))
            ->placeholder('-- اختر المخزن الفرعي --')
            ->required()
        }}
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        {{ BsForm::text('driver_name')
            ->label(trans('outbound_shipments.attributes.driver_name'))
            ->placeholder('اسم السائق')
        }}
    </div>
    <div class="col-md-6">
        {{ BsForm::textarea('notes')
            ->label(trans('outbound_shipments.attributes.notes'))
            ->rows(2)
        }}
    </div>
</div>

<hr>

<!-- بيان الصادر -->
<h4 class="mt-3 mb-3"><i class="fas fa-box"></i> بيان الصادر</h4>

<div id="shipment_items_container">
    <div class="shipment-item-row border p-3 mb-3 rounded bg-light">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="shipment_items_0_type">نوع الطرد <span class="text-danger">*</span></label>
                    <select name="shipment_items[0][type]" id="shipment_items_0_type" class="form-control" required onchange="loadPackages(this, 0)">
                        <option value="">-- اختر النوع --</option>
                        <option value="ready_package">طرد جاهز</option>
                        <option value="internal_package">طرد داخلي</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="shipment_items_0_package_id">الطرد <span class="text-danger">*</span></label>
                    <select name="shipment_items[0][package_id]" id="shipment_items_0_package_id" class="form-control" required>
                        <option value="">-- اختر الطرد --</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="shipment_items_0_quantity">الكمية <span class="text-danger">*</span></label>
                    <input type="number" name="shipment_items[0][quantity]" id="shipment_items_0_quantity" class="form-control" min="1" value="1" required>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="shipment_items_0_weight">الوزن (كجم)</label>
                    <input type="number" name="shipment_items[0][weight]" id="shipment_items_0_weight" class="form-control" step="0.01" min="0">
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <div class="form-group w-100">
                    <button type="button" class="btn btn-danger btn-block" onclick="removeShipmentItemRow(this)">
                        <i class="fas fa-trash"></i> حذف
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<button type="button" class="btn btn-success mb-3" onclick="addShipmentItemRow()">
    <i class="fas fa-plus"></i> إضافة طرد
</button>

@push('scripts')
<script>
    let shipmentItemIndex = 1;
    
    // بيانات الطرود
    const readyPackages = @json(App\Models\ReadyPackage::select('id', 'name', 'weight')->get());
    const internalPackages = @json(App\Models\InternalPackage::select('id', 'name', 'weight')->get());
    
    function loadPackages(selectElement, index) {
        const type = selectElement.value;
        const packageSelect = document.getElementById(`shipment_items_${index}_package_id`);
        const weightInput = document.getElementById(`shipment_items_${index}_weight`);
        
        // تفريغ القائمة
        packageSelect.innerHTML = '<option value="">-- اختر الطرد --</option>';
        
        let packages = [];
        if (type === 'ready_package') {
            packages = readyPackages;
        } else if (type === 'internal_package') {
            packages = internalPackages;
        }
        
        packages.forEach(pkg => {
            const option = document.createElement('option');
            option.value = pkg.id;
            option.textContent = pkg.name;
            option.dataset.weight = pkg.weight || 0;
            packageSelect.appendChild(option);
        });
        
        // عند تغيير الطرد، تحديث الوزن تلقائياً
        packageSelect.onchange = function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.dataset.weight) {
                weightInput.value = selectedOption.dataset.weight;
            }
        };
    }
    
    function addShipmentItemRow() {
        const container = document.getElementById('shipment_items_container');
        const newRow = `
            <div class="shipment-item-row border p-3 mb-3 rounded bg-light">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="shipment_items_${shipmentItemIndex}_type">نوع الطرد <span class="text-danger">*</span></label>
                            <select name="shipment_items[${shipmentItemIndex}][type]" id="shipment_items_${shipmentItemIndex}_type" class="form-control" required onchange="loadPackages(this, ${shipmentItemIndex})">
                                <option value="">-- اختر النوع --</option>
                                <option value="ready_package">طرد جاهز</option>
                                <option value="internal_package">طرد داخلي</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="shipment_items_${shipmentItemIndex}_package_id">الطرد <span class="text-danger">*</span></label>
                            <select name="shipment_items[${shipmentItemIndex}][package_id]" id="shipment_items_${shipmentItemIndex}_package_id" class="form-control" required>
                                <option value="">-- اختر الطرد --</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="shipment_items_${shipmentItemIndex}_quantity">الكمية <span class="text-danger">*</span></label>
                            <input type="number" name="shipment_items[${shipmentItemIndex}][quantity]" id="shipment_items_${shipmentItemIndex}_quantity" class="form-control" min="1" value="1" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="shipment_items_${shipmentItemIndex}_weight">الوزن (كجم)</label>
                            <input type="number" name="shipment_items[${shipmentItemIndex}][weight]" id="shipment_items_${shipmentItemIndex}_weight" class="form-control" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button type="button" class="btn btn-danger btn-block" onclick="removeShipmentItemRow(this)">
                                <i class="fas fa-trash"></i> حذف
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newRow);
        shipmentItemIndex++;
    }
    
    function removeShipmentItemRow(button) {
        const container = document.getElementById('shipment_items_container');
        const rows = container.getElementsByClassName('shipment-item-row');
        
        if (rows.length > 1) {
            button.closest('.shipment-item-row').remove();
        } else {
            alert('يجب أن يكون هناك طرد واحد على الأقل');
        }
    }
</script>
@endpush
