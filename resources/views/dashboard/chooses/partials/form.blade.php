@include('dashboard.errors')

@component('dashboard::components.box')
    @slot('title')
        <i class="fas fa-edit text-primary"></i> @lang('chooses.sections.basic_info')
    @endslot

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label font-weight-bold">
                <i class="fas fa-tag text-info"></i> @lang('chooses.attributes.name')
                <span class="text-danger">*</span>
            </label>
            {{ BsForm::text('name')->required()->placeholder(trans('chooses.attributes.name'))->label(false) }}
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label font-weight-bold">
                <i class="fas fa-code text-info"></i> @lang('chooses.attributes.slug')
            </label>
            {{ BsForm::text('slug')->placeholder(trans('chooses.attributes.slug'))->label(false) }}
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label font-weight-bold">
                <i class="fas fa-sort-numeric-down text-info"></i> @lang('chooses.attributes.order')
            </label>
            {{ BsForm::number('order')->min(0)->value(old('order', $choose->order ?? 0))->label(false) }}
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label font-weight-bold">
                <i class="fas fa-cogs text-info"></i> @lang('chooses.attributes.type')
                <span class="text-danger">*</span>
            </label>
            {{ BsForm::select('type')
                ->options(trans('chooses.types'))
                ->value(request('type', $choose->type ?? ''))
                ->required()
                ->label(false) }}
        </div>
    </div>

    @slot('footer')
        <button type="submit" class="btn btn-primary px-4 shadow-sm">
            <i class="fas fa-save mr-1"></i> @lang('chooses.actions.save')
        </button>
    @endslot
@endcomponent

