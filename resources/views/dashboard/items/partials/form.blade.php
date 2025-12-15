@include('dashboard.errors')

<div class="row">
    <div class="col-md-12">
        {{ BsForm::text('name')->required() }}
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        {{ BsForm::textarea('description')->label(trans('items.attributes.description')) }}
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        {{ BsForm::checkbox('package')->label(trans('items.attributes.package')) }}
    </div>
    <div class="col-md-4">
        {{ BsForm::number('type')->label(trans('items.attributes.type'))->min(0)->max(255) }}
    </div>
    <div class="col-md-4">
        {{ BsForm::number('weight')->label(trans('items.attributes.weight'))->step('0.01')->min(0) }}
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        {{ BsForm::number('quantity')->label(trans('items.attributes.quantity'))->min(0) }}
    </div>
</div>

