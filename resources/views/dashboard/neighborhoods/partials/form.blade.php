@include('dashboard.errors')

@component('dashboard::components.box')
    @slot('title')
        <i class="fas fa-edit text-primary"></i> @lang('neighborhoods.sections.basic_info')
    @endslot

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label font-weight-bold">
                <i class="fas fa-map-marked-alt text-info"></i> @lang('neighborhoods.attributes.name')
                <span class="text-danger">*</span>
            </label>
            {{ BsForm::text('name')->required()->placeholder(trans('neighborhoods.placeholders.name'))->label(false) }}
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label font-weight-bold">
                <i class="fas fa-city text-info"></i> @lang('cities.singular')
                <span class="text-danger">*</span>
            </label>
            {{ BsForm::select('city_id')
                ->options(\App\Models\City::pluck('name', 'id'))
                ->required()
                ->placeholder(trans('cities.select'))
                ->label(false) }}
        </div>
    </div>

    @slot('footer')
        <button type="submit" class="btn btn-primary px-4 shadow-sm">
            <i class="fas fa-save mr-1"></i> @lang('neighborhoods.actions.save')
        </button>
    @endslot
@endcomponent

