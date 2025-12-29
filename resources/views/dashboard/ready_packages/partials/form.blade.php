@include('dashboard.errors')

<div class="row">
    <div class="col-md-6">
        {{ BsForm::select('inbound_shipment_id')
            ->label(trans('ready_packages.attributes.inbound_shipment_id'))
            ->options(App\Models\InboundShipment::where('inbound_type', 'ready_package')->pluck('shipment_number', 'id'))
            ->placeholder('-- اختر إرسالية الوارد --')
            ->required()
        }}
    </div>
    <div class="col-md-6">
        {{ BsForm::text('name')
            ->label(trans('ready_packages.attributes.name'))
            ->required()
        }}
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        {{ BsForm::number('quantity')
            ->label(trans('ready_packages.attributes.quantity'))
            ->min(1)
            ->value(old('quantity', $ready_package->quantity ?? 1))
        }}
    </div>
    <div class="col-md-6">
        {{ BsForm::number('weight')
            ->label(trans('ready_packages.attributes.weight'))
            ->attribute('step', '0.01')
            ->min(0)
            ->placeholder('الوزن بالكيلوجرام')
        }}
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        {{ BsForm::textarea('description')
            ->label(trans('ready_packages.attributes.description'))
            ->rows(3)
        }}
    </div>
</div>
