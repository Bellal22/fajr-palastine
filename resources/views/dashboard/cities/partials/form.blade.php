@include('dashboard.errors')

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-city"></i> @lang('cities.sections.city_info')
                </h5>
            </div>
            <div class="card-body">
                {{ BsForm::text('name')
                    ->label(trans('cities.attributes.name'))
                    ->placeholder(trans('cities.placeholders.name'))
                    ->required() }}
            </div>
        </div>
    </div>
</div>
