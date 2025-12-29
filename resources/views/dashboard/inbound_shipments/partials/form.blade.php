@include('dashboard.errors')

<div class="row">
    <div class="col-md-6">
        {{ BsForm::select('supplier_id')
            ->label(trans('inbound_shipments.attributes.supplier_id'))
            ->options(App\Models\Supplier::where('type', 'donor')->pluck('name', 'id'))
            ->placeholder(trans('inbound_shipments.placeholders.supplier'))
            ->required()
        }}
    </div>
    <div class="col-md-6">
        {{ BsForm::text('shipment_number')
            ->label(trans('inbound_shipments.attributes.shipment_number'))
            ->value($shipment_number ?? null)
            ->required()
        }}
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        {{ BsForm::select('inbound_type')
            ->label(trans('inbound_shipments.attributes.inbound_type'))
            ->options([
                'single_item' => trans('inbound_shipments.types.single_item'),
                'ready_package' => trans('inbound_shipments.types.ready_package'),
            ])
            ->placeholder(trans('inbound_shipments.placeholders.inbound_type'))
            ->required()
            ->attribute('id', 'inbound_type')
            ->attribute('onchange', 'toggleInboundType()')
        }}
    </div>
</div>

<!-- قسم الأصناف المفردة -->
<div id="single_items_section" style="display:none;">
    <h4 class="mt-3 mb-3">@lang('inbound_shipments.sections.single_items')</h4>
    <div id="items_container">
        <div class="item-row border p-3 mb-3 rounded">
            <div class="row">
                <div class="col-md-3">
                    {{ BsForm::text('items[0][name]')->label(trans('inbound_shipments.attributes.item_name'))->required() }}
                </div>
                <div class="col-md-3">
                    {{ BsForm::textarea('items[0][description]')->label(trans('inbound_shipments.attributes.description'))->rows(2) }}
                </div>
                <div class="col-md-2">
                    {{ BsForm::number('items[0][quantity]')->label(trans('inbound_shipments.attributes.quantity'))->min(1)->required() }}
                </div>
                <div class="col-md-2">
                    {{ BsForm::number('items[0][weight]')->label(trans('inbound_shipments.attributes.weight_kg'))->attribute('step', '0.01')->min(0) }}
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-block" onclick="removeItemRow(this)">
                        <i class="fas fa-trash"></i> @lang('inbound_shipments.buttons.remove')
                    </button>
                </div>
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-success mb-3" onclick="addItemRow()">
        <i class="fas fa-plus"></i> @lang('inbound_shipments.buttons.add_item')
    </button>
</div>

<!-- قسم الطرود الجاهزة -->
<div id="ready_packages_section" style="display:none;">
    <h4 class="mt-3 mb-3">@lang('inbound_shipments.sections.ready_packages')</h4>
    <div id="packages_container">
        <div class="package-row border p-3 mb-3 rounded">
            <div class="row">
                <div class="col-md-3">
                    {{ BsForm::text('packages[0][name]')->label(trans('inbound_shipments.attributes.package_name'))->required() }}
                </div>
                <div class="col-md-3">
                    {{ BsForm::textarea('packages[0][description]')->label(trans('inbound_shipments.attributes.description'))->rows(2) }}
                </div>
                <div class="col-md-2">
                    {{ BsForm::number('packages[0][quantity]')->label(trans('inbound_shipments.attributes.quantity'))->min(1)->value(1) }}
                </div>
                <div class="col-md-2">
                    {{ BsForm::number('packages[0][weight]')->label(trans('inbound_shipments.attributes.weight_kg'))->attribute('step', '0.01')->min(0) }}
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-block" onclick="removePackageRow(this)">
                        <i class="fas fa-trash"></i> @lang('inbound_shipments.buttons.remove')
                    </button>
                </div>
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-success mb-3" onclick="addPackageRow()">
        <i class="fas fa-plus"></i> @lang('inbound_shipments.buttons.add_package')
    </button>
</div>

<div class="row">
    <div class="col-md-12">
        {{ BsForm::textarea('notes')->label(trans('inbound_shipments.attributes.notes'))->rows(3) }}
    </div>
</div>

@push('scripts')
    <script>
        let itemIndex = 1;
        let packageIndex = 1;

        function toggleInboundType() {
            const type = document.getElementById('inbound_type').value;

            if (type === 'single_item') {
                document.getElementById('single_items_section').style.display = 'block';
                enableInputs('single_items_section');

                document.getElementById('ready_packages_section').style.display = 'none';
                disableInputs('ready_packages_section');
            } else if (type === 'ready_package') {
                document.getElementById('single_items_section').style.display = 'none';
                disableInputs('single_items_section');

                document.getElementById('ready_packages_section').style.display = 'block';
                enableInputs('ready_packages_section');
            } else {
                document.getElementById('single_items_section').style.display = 'none';
                disableInputs('single_items_section');

                document.getElementById('ready_packages_section').style.display = 'none';
                disableInputs('ready_packages_section');
            }
        }

        function disableInputs(sectionId) {
            const section = document.getElementById(sectionId);
            const inputs = section.querySelectorAll('input, select, textarea, button');
            inputs.forEach(input => input.disabled = true);
        }

        function enableInputs(sectionId) {
            const section = document.getElementById(sectionId);
            const inputs = section.querySelectorAll('input, select, textarea, button');
            inputs.forEach(input => input.disabled = false);
        }

        function addItemRow() {
            const container = document.getElementById('items_container');
            const newRow = `
                <div class="item-row border p-3 mb-3 rounded">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="items_${itemIndex}_name">@lang('inbound_shipments.attributes.item_name') <span class="text-danger">*</span></label>
                                <input type="text" name="items[${itemIndex}][name]" id="items_${itemIndex}_name" class="form-control" placeholder="@lang('inbound_shipments.placeholders.item_name')" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="items_${itemIndex}_description">@lang('inbound_shipments.attributes.description')</label>
                                <textarea name="items[${itemIndex}][description]" id="items_${itemIndex}_description" class="form-control" rows="2" placeholder="@lang('inbound_shipments.placeholders.description')"></textarea>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="items_${itemIndex}_quantity">@lang('inbound_shipments.attributes.quantity') <span class="text-danger">*</span></label>
                                <input type="number" name="items[${itemIndex}][quantity]" id="items_${itemIndex}_quantity" class="form-control" min="1" placeholder="@lang('inbound_shipments.placeholders.quantity')" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="items_${itemIndex}_weight">@lang('inbound_shipments.attributes.weight_kg')</label>
                                <input type="number" name="items[${itemIndex}][weight]" id="items_${itemIndex}_weight" class="form-control" step="0.01" min="0" placeholder="@lang('inbound_shipments.placeholders.weight')">
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="form-group w-100">
                                <button type="button" class="btn btn-danger btn-block" onclick="removeItemRow(this)">
                                    <i class="fas fa-trash"></i> @lang('inbound_shipments.buttons.remove')
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', newRow);
            itemIndex++;
        }

        function removeItemRow(button) {
            const container = document.getElementById('items_container');
            const rows = container.getElementsByClassName('item-row');

            if (rows.length > 1) {
                button.closest('.item-row').remove();
            } else {
                alert("@lang('inbound_shipments.alerts.min_item')");
            }
        }

        function addPackageRow() {
            const container = document.getElementById('packages_container');
            const newRow = `
                <div class="package-row border p-3 mb-3 rounded">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="packages_${packageIndex}_name">@lang('inbound_shipments.attributes.package_name') <span class="text-danger">*</span></label>
                                <input type="text" name="packages[${packageIndex}][name]" id="packages_${packageIndex}_name" class="form-control" placeholder="@lang('inbound_shipments.placeholders.package_name')" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="packages_${packageIndex}_description">@lang('inbound_shipments.attributes.description')</label>
                                <textarea name="packages[${packageIndex}][description]" id="packages_${packageIndex}_description" class="form-control" rows="2" placeholder="@lang('inbound_shipments.placeholders.description')"></textarea>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="packages_${packageIndex}_quantity">@lang('inbound_shipments.attributes.quantity')</label>
                                <input type="number" name="packages[${packageIndex}][quantity]" id="packages_${packageIndex}_quantity" class="form-control" min="1" value="1" placeholder="@lang('inbound_shipments.placeholders.quantity')">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="packages_${packageIndex}_weight">@lang('inbound_shipments.attributes.weight_kg')</label>
                                <input type="number" name="packages[${packageIndex}][weight]" id="packages_${packageIndex}_weight" class="form-control" step="0.01" min="0" placeholder="@lang('inbound_shipments.placeholders.weight')">
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="form-group w-100">
                                <button type="button" class="btn btn-danger btn-block" onclick="removePackageRow(this)">
                                    <i class="fas fa-trash"></i> @lang('inbound_shipments.buttons.remove')
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', newRow);
            packageIndex++;
        }

        function removePackageRow(button) {
            const container = document.getElementById('packages_container');
            const rows = container.getElementsByClassName('package-row');

            if (rows.length > 1) {
                button.closest('.package-row').remove();
            } else {
                alert("@lang('inbound_shipments.alerts.min_package')");
            }
        }

        // عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            toggleInboundType();
        });
    </script>
@endpush
