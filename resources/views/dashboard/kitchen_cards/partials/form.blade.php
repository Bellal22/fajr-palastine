@include('dashboard.errors')

<div class="row">
    <div class="col-md-6">
        {{ BsForm::text('camp_name')->label(trans('kitchen_cards.attributes.camp_name'))->required() }}
    </div>
    <div class="col-md-6">
        {{ BsForm::text('card_number')->label(trans('kitchen_cards.attributes.card_number'))->required() }}
    </div>
</div>
