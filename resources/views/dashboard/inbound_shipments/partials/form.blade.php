@include('dashboard.errors')

<div class="row">
    <div class="col-md-12">
        {{ BsForm::text('name')->required() }}
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        {{ BsForm::textarea('description')->label(trans('inbound_shipments.attributes.description')) }}
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        {{ BsForm::number('supplier_id')->label(trans('inbound_shipments.attributes.supplier_id'))->min(1) }}
    </div>
    <div class="col-md-4">
        {{ BsForm::checkbox('type')->label(trans('inbound_shipments.attributes.type')) }}
    </div>
    <div class="col-md-4">
        {{ BsForm::number('weight')->label(trans('inbound_shipments.attributes.weight'))->step('0.01')->min(0) }}
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        {{ BsForm::number('quantity')->label(trans('inbound_shipments.attributes.quantity'))->min(0) }}
    </div>
</div>

