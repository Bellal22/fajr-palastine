@include('dashboard.errors')

{{-- معلومات الطرد الأساسية --}}
<div class="card border-primary mb-3">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-info-circle"></i> @lang('ready_packages.sections.basic_info')
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="mb-1 font-weight-bold">
                    <i class="fas fa-shipping-fast text-info"></i>
                    @lang('ready_packages.attributes.inbound_shipment_id')
                    <span class="text-danger">*</span>
                </label>
                {{ BsForm::select('inbound_shipment_id')
                    ->options(App\Models\InboundShipment::where('inbound_type', 'ready_package')->pluck('shipment_number', 'id'))
                    ->placeholder(trans('ready_packages.placeholders.inbound_shipment'))
                    ->required()
                    ->label(false)
                }}
            </div>

            <div class="col-md-6 mb-3">
                <label class="mb-1 font-weight-bold">
                    <i class="fas fa-box text-primary"></i>
                    @lang('ready_packages.attributes.name')
                    <span class="text-danger">*</span>
                </label>
                {{ BsForm::text('name')
                    ->placeholder(trans('ready_packages.placeholders.name'))
                    ->required()
                    ->label(false)
                }}
            </div>

            <div class="col-md-12 mb-3">
                <label class="mb-1 font-weight-bold">
                    <i class="fas fa-align-right text-secondary"></i>
                    @lang('ready_packages.attributes.description')
                </label>
                {{ BsForm::textarea('description')
                    ->placeholder(trans('ready_packages.placeholders.description'))
                    ->rows(3)
                    ->label(false)
                }}
            </div>
        </div>
    </div>
</div>

{{-- التفاصيل والخصائص --}}
<div class="card border-success mb-3">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">
            <i class="fas fa-cogs"></i> @lang('ready_packages.sections.properties')
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="mb-1 font-weight-bold">
                    <i class="fas fa-sort-numeric-up text-success"></i>
                    @lang('ready_packages.attributes.quantity')
                </label>
                {{ BsForm::number('quantity')
                    ->placeholder(trans('ready_packages.placeholders.quantity'))
                    ->min(1)
                    ->value(old('quantity', $ready_package->quantity ?? 1))
                    ->label(false)
                }}
                <small class="text-muted">@lang('ready_packages.hints.quantity')</small>
            </div>

            <div class="col-md-6 mb-3">
                <label class="mb-1 font-weight-bold">
                    <i class="fas fa-weight text-warning"></i>
                    @lang('ready_packages.attributes.weight')
                </label>
                {{ BsForm::number('weight')
                    ->placeholder(trans('ready_packages.placeholders.weight'))
                    ->attribute('step', '0.01')
                    ->min(0)
                    ->label(false)
                }}
                <small class="text-muted">@lang('ready_packages.hints.weight')</small>
            </div>
        </div>
    </div>
</div>
