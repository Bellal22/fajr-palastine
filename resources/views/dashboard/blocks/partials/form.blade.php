<!-- resources/views/dashboard/blocks/partials/form.blade.php -->

@include('dashboard.errors')

<div class="row">
    {{-- المعلومات الأساسية --}}
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle"></i> @lang('blocks.sections.basic_info')
                </h5>
            </div>
            <div class="card-body">
                {{ BsForm::text('name')
                    ->label(trans('blocks.attributes.name'))
                    ->placeholder(trans('blocks.placeholders.name'))
                    ->required() }}

                @if(auth()->user()->isAdmin())
                {{ BsForm::select('area_responsible_id')
                    ->label(trans('blocks.attributes.area_responsible'))
                    ->options($areaResponsibles ?? [])
                    ->value(isset($block) ? $block->area_responsible_id : request('area_responsible_id'))
                    ->placeholder(trans('blocks.placeholders.select_area_responsible'))
                    ->required() }}
                @endif

                {{ BsForm::text('title')
                    ->label(trans('blocks.attributes.title'))
                    ->placeholder(trans('blocks.placeholders.title'))
                    ->required() }}
            </div>
        </div>
    </div>

    {{-- بيانات الاتصال --}}
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-address-card"></i> @lang('blocks.sections.contact_info')
                </h5>
            </div>
            <div class="card-body">
                {{ BsForm::text('phone')
                    ->label(trans('blocks.attributes.phone'))
                    ->placeholder(trans('blocks.placeholders.phone'))
                    ->required() }}

                {{ BsForm::textarea('note')
                    ->label(trans('blocks.attributes.note'))
                    ->placeholder(trans('blocks.placeholders.note'))
                    ->rows(4) }}

                <small class="form-text text-muted">
                    <i class="fas fa-info-circle"></i>
                    @lang('blocks.hints.note')
                </small>
            </div>
        </div>
    </div>
</div>

{{-- الموقع الجغرافي --}}
<div class="card mb-4">
    <div class="card-header bg-gray text-dark">
        <h5 class="mb-0">
            <i class="fas fa-map-marked-alt"></i> @lang('blocks.sections.location')
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                {{ BsForm::text('lat')
                    ->label(trans('blocks.attributes.lat'))
                    ->placeholder(trans('blocks.placeholders.lat'))
                    ->required() }}
            </div>
            <div class="col-md-6">
                {{ BsForm::text('lan')
                    ->label(trans('blocks.attributes.lan'))
                    ->placeholder(trans('blocks.placeholders.lan'))
                    ->required() }}
            </div>
        </div>

        <div class="alert alert-info mb-0 mt-3">
            <i class="fas fa-lightbulb"></i>
            <strong>@lang('blocks.hints.location_title')</strong>
            <ul class="mb-0 mt-2">
                <li>@lang('blocks.hints.location_format')</li>
                <li>@lang('blocks.hints.location_example')</li>
            </ul>
        </div>
    </div>
</div>
