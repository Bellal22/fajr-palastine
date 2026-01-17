@include('dashboard.errors')

{{-- معلومات الشحنة الأساسية --}}
<div class="card border-primary mb-3">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-info-circle"></i> @lang('inbound_shipments.sections.basic_info')
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="mb-1 font-weight-bold">
                    <i class="fas fa-truck text-primary"></i>
                    @lang('inbound_shipments.attributes.supplier_id')
                    <span class="text-danger">*</span>
                </label>
                {{ BsForm::select('supplier_id')
                    ->options(App\Models\Supplier::where('type', 'donor')->pluck('name', 'id'))
                    ->placeholder(trans('inbound_shipments.placeholders.supplier'))
                    ->required()
                    ->label(false)
                }}
            </div>

            <div class="col-md-6 mb-3">
                <label class="mb-1 font-weight-bold">
                    <i class="fas fa-barcode text-success"></i>
                    @lang('inbound_shipments.attributes.shipment_number')
                    <span class="text-danger">*</span>
                </label>
                {{ BsForm::text('shipment_number')
                    ->value($shipment_number ?? null)
                    ->placeholder(trans('inbound_shipments.placeholders.shipment_number'))
                    ->required()
                    ->label(false)
                }}
            </div>

            <div class="col-md-12 mb-3">
                <label class="mb-1 font-weight-bold">
                    <i class="fas fa-layer-group text-info"></i>
                    @lang('inbound_shipments.attributes.inbound_type')
                    <span class="text-danger">*</span>
                </label>
                {{ BsForm::select('inbound_type')
                    ->options([
                        'single_item' => trans('inbound_shipments.types.single_item'),
                        'ready_package' => trans('inbound_shipments.types.ready_package'),
                    ])
                    ->placeholder(trans('inbound_shipments.placeholders.inbound_type'))
                    ->required()
                    ->attribute('id', 'inbound_type')
                    ->attribute('onchange', 'toggleInboundType()')
                    ->label(false)
                }}
            </div>
        </div>
    </div>
</div>

{{-- قسم الأصناف المفردة --}}
<div id="single_items_section" style="display:none;">
    <div class="card border-success mb-3">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <i class="fas fa-box"></i> @lang('inbound_shipments.sections.single_items')
            </h5>
        </div>
        <div class="card-body">
            <div id="items_container">
                <div class="item-row border p-3 mb-3 rounded bg-light">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label class="mb-1 font-weight-bold">
                                <i class="fas fa-tag text-primary"></i>
                                @lang('inbound_shipments.attributes.item_name')
                                <span class="text-danger">*</span>
                            </label>
                            {{ BsForm::text('items[0][name]')
                                ->placeholder(trans('inbound_shipments.placeholders.item_name'))
                                ->required()
                                ->label(false)
                            }}
                        </div>

                        <div class="col-md-3 mb-2">
                            <label class="mb-1 font-weight-bold">
                                <i class="fas fa-align-right text-secondary"></i>
                                @lang('inbound_shipments.attributes.description')
                            </label>
                            {{ BsForm::textarea('items[0][description]')
                                ->placeholder(trans('inbound_shipments.placeholders.description'))
                                ->rows(2)
                                ->label(false)
                            }}
                        </div>

                        <div class="col-md-2 mb-2">
                            <label class="mb-1 font-weight-bold">
                                <i class="fas fa-sort-numeric-up text-info"></i>
                                @lang('inbound_shipments.attributes.quantity')
                                <span class="text-danger">*</span>
                            </label>
                            {{ BsForm::number('items[0][quantity]')
                                ->min(1)
                                ->placeholder(trans('inbound_shipments.placeholders.quantity'))
                                ->required()
                                ->label(false)
                            }}
                        </div>

                        <div class="col-md-2 mb-2">
                            <label class="mb-1 font-weight-bold">
                                <i class="fas fa-weight text-warning"></i>
                                @lang('inbound_shipments.attributes.weight_kg')
                            </label>
                            {{ BsForm::number('items[0][weight]')
                                ->attribute('step', '0.01')
                                ->min(0)
                                ->placeholder(trans('inbound_shipments.placeholders.weight'))
                                ->label(false)
                            }}
                        </div>

                        <div class="col-md-2 d-flex align-items-end mb-2">
                            <button type="button" class="btn btn-danger btn-block" onclick="removeItemRow(this)">
                                <i class="fas fa-trash"></i> @lang('inbound_shipments.buttons.remove')
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-success" onclick="addItemRow()">
                <i class="fas fa-plus"></i> @lang('inbound_shipments.buttons.add_item')
            </button>
        </div>
    </div>
</div>

{{-- قسم الطرود الجاهزة --}}
<div id="ready_packages_section" style="display:none;">
    <div class="card border-info mb-3">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                <i class="fas fa-boxes"></i> @lang('inbound_shipments.sections.ready_packages')
            </h5>
        </div>
        <div class="card-body">
            <div id="packages_container">
                <div class="package-row border p-3 mb-3 rounded bg-light">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label class="mb-1 font-weight-bold">
                                <i class="fas fa-box-open text-primary"></i>
                                @lang('inbound_shipments.attributes.package_name')
                                <span class="text-danger">*</span>
                            </label>
                            {{ BsForm::text('packages[0][name]')
                                ->placeholder(trans('inbound_shipments.placeholders.package_name'))
                                ->required()
                                ->label(false)
                            }}
                        </div>

                        <div class="col-md-3 mb-2">
                            <label class="mb-1 font-weight-bold">
                                <i class="fas fa-align-right text-secondary"></i>
                                @lang('inbound_shipments.attributes.description')
                            </label>
                            {{ BsForm::textarea('packages[0][description]')
                                ->placeholder(trans('inbound_shipments.placeholders.description'))
                                ->rows(2)
                                ->label(false)
                            }}
                        </div>

                        <div class="col-md-2 mb-2">
                            <label class="mb-1 font-weight-bold">
                                <i class="fas fa-sort-numeric-up text-info"></i>
                                @lang('inbound_shipments.attributes.quantity')
                            </label>
                            {{ BsForm::number('packages[0][quantity]')
                                ->min(1)
                                ->value(1)
                                ->placeholder(trans('inbound_shipments.placeholders.quantity'))
                                ->label(false)
                            }}
                        </div>

                        <div class="col-md-2 mb-2">
                            <label class="mb-1 font-weight-bold">
                                <i class="fas fa-weight text-warning"></i>
                                @lang('inbound_shipments.attributes.weight_kg')
                            </label>
                            {{ BsForm::number('packages[0][weight]')
                                ->attribute('step', '0.01')
                                ->min(0)
                                ->placeholder(trans('inbound_shipments.placeholders.weight'))
                                ->label(false)
                            }}
                        </div>

                        <div class="col-md-2 d-flex align-items-end mb-2">
                            <button type="button" class="btn btn-danger btn-block" onclick="removePackageRow(this)">
                                <i class="fas fa-trash"></i> @lang('inbound_shipments.buttons.remove')
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-success" onclick="addPackageRow()">
                <i class="fas fa-plus"></i> @lang('inbound_shipments.buttons.add_package')
            </button>
        </div>
    </div>
</div>

{{-- ملاحظات --}}
<div class="card border-warning mb-3">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">
            <i class="fas fa-sticky-note"></i> @lang('inbound_shipments.sections.notes')
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <label class="mb-1 font-weight-bold">
                    <i class="fas fa-comment text-secondary"></i>
                    @lang('inbound_shipments.attributes.notes')
                </label>
                {{ BsForm::textarea('notes')
                    ->placeholder(trans('inbound_shipments.placeholders.notes'))
                    ->rows(3)
                    ->label(false)
                }}
            </div>
        </div>
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
                <div class="item-row border p-3 mb-3 rounded bg-light">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label class="mb-1 font-weight-bold">
                                <i class="fas fa-tag text-primary"></i>
                                @lang('inbound_shipments.attributes.item_name')
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="items[${itemIndex}][name]" class="form-control" placeholder="@lang('inbound_shipments.placeholders.item_name')" required>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="mb-1 font-weight-bold">
                                <i class="fas fa-align-right text-secondary"></i>
                                @lang('inbound_shipments.attributes.description')
                            </label>
                            <textarea name="items[${itemIndex}][description]" class="form-control" rows="2" placeholder="@lang('inbound_shipments.placeholders.description')"></textarea>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="mb-1 font-weight-bold">
                                <i class="fas fa-sort-numeric-up text-info"></i>
                                @lang('inbound_shipments.attributes.quantity')
                                <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="items[${itemIndex}][quantity]" class="form-control" min="1" placeholder="@lang('inbound_shipments.placeholders.quantity')" required>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="mb-1 font-weight-bold">
                                <i class="fas fa-weight text-warning"></i>
                                @lang('inbound_shipments.attributes.weight_kg')
                            </label>
                            <input type="number" name="items[${itemIndex}][weight]" class="form-control" step="0.01" min="0" placeholder="@lang('inbound_shipments.placeholders.weight')">
                        </div>
                        <div class="col-md-2 d-flex align-items-end mb-2">
                            <button type="button" class="btn btn-danger btn-block" onclick="removeItemRow(this)">
                                <i class="fas fa-trash"></i> @lang('inbound_shipments.buttons.remove')
                            </button>
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
                <div class="package-row border p-3 mb-3 rounded bg-light">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label class="mb-1 font-weight-bold">
                                <i class="fas fa-box-open text-primary"></i>
                                @lang('inbound_shipments.attributes.package_name')
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="packages[${packageIndex}][name]" class="form-control" placeholder="@lang('inbound_shipments.placeholders.package_name')" required>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="mb-1 font-weight-bold">
                                <i class="fas fa-align-right text-secondary"></i>
                                @lang('inbound_shipments.attributes.description')
                            </label>
                            <textarea name="packages[${packageIndex}][description]" class="form-control" rows="2" placeholder="@lang('inbound_shipments.placeholders.description')"></textarea>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="mb-1 font-weight-bold">
                                <i class="fas fa-sort-numeric-up text-info"></i>
                                @lang('inbound_shipments.attributes.quantity')
                            </label>
                            <input type="number" name="packages[${packageIndex}][quantity]" class="form-control" min="1" value="1" placeholder="@lang('inbound_shipments.placeholders.quantity')">
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="mb-1 font-weight-bold">
                                <i class="fas fa-weight text-warning"></i>
                                @lang('inbound_shipments.attributes.weight_kg')
                            </label>
                            <input type="number" name="packages[${packageIndex}][weight]" class="form-control" step="0.01" min="0" placeholder="@lang('inbound_shipments.placeholders.weight')">
                        </div>
                        <div class="col-md-2 d-flex align-items-end mb-2">
                            <button type="button" class="btn btn-danger btn-block" onclick="removePackageRow(this)">
                                <i class="fas fa-trash"></i> @lang('inbound_shipments.buttons.remove')
                            </button>
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

        document.addEventListener('DOMContentLoaded', function() {
            toggleInboundType();
        });
    </script>
@endpush
