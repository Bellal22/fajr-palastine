@include('dashboard.errors')

{{-- معلومات الصنف الأساسية --}}
<div class="card border-primary mb-3">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-info-circle"></i> @lang('items.sections.basic_info')
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="mb-1 font-weight-bold">
                    <i class="fas fa-tag text-primary"></i>
                    @lang('items.attributes.name')
                    <span class="text-danger">*</span>
                </label>
                {{ BsForm::text('name')
                    ->placeholder(trans('items.placeholders.name'))
                    ->required()
                    ->label(false)
                }}
            </div>

            <div class="col-md-12 mb-3">
                <label class="mb-1 font-weight-bold">
                    <i class="fas fa-align-right text-secondary"></i>
                    @lang('items.attributes.description')
                </label>
                {{ BsForm::textarea('description')
                    ->placeholder(trans('items.placeholders.description'))
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
            <i class="fas fa-cogs"></i> @lang('items.sections.properties')
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="mb-1 font-weight-bold">
                    <i class="fas fa-layer-group text-info"></i>
                    @lang('items.attributes.type')
                </label>
                {{ BsForm::number('type')
                    ->placeholder(trans('items.placeholders.type'))
                    ->min(0)
                    ->max(255)
                    ->label(false)
                }}
                <small class="text-muted">@lang('items.hints.type')</small>
            </div>

            <div class="col-md-4 mb-3">
                <label class="mb-1 font-weight-bold">
                    <i class="fas fa-weight text-warning"></i>
                    @lang('items.attributes.weight')
                </label>
                {{ BsForm::number('weight')
                    ->placeholder(trans('items.placeholders.weight'))
                    ->step('0.01')
                    ->min(0)
                    ->label(false)
                }}
                <small class="text-muted">@lang('items.hints.weight')</small>
            </div>

            <div class="col-md-4 mb-3">
                <label class="mb-1 font-weight-bold">
                    <i class="fas fa-sort-numeric-up text-success"></i>
                    @lang('items.attributes.quantity')
                </label>
                {{ BsForm::number('quantity')
                    ->placeholder(trans('items.placeholders.quantity'))
                    ->min(0)
                    ->label(false)
                }}
                <small class="text-muted">@lang('items.hints.quantity')</small>
            </div>
        </div>
    </div>
</div>

{{-- الإعدادات الإضافية --}}
{{-- <div class="card border-info mb-3">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">
            <i class="fas fa-toggle-on"></i> @lang('items.sections.additional_settings')
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="custom-control custom-checkbox">
                    {{ BsForm::checkbox('package')
                        ->label(false)
                        ->attribute('class', 'custom-control-input')
                        ->attribute('id', 'package')
                    }}
                    <label class="custom-control-label font-weight-bold" for="package">
                        <i class="fas fa-box text-primary"></i>
                        @lang('items.attributes.package')
                    </label>
                    <br>
                    <small class="text-muted {{ app()->isLocale('ar') ? 'mr-4' : 'ml-4' }}">
                        @lang('items.hints.package')
                    </small>
                </div>
            </div>
        </div>
    </div>
</div> --}}
