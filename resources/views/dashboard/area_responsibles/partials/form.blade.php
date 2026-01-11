<!-- resources/views/dashboard/area_responsibles/partials/form.blade.php -->

@include('dashboard.errors')

<div class="row">
    {{-- المعلومات الأساسية --}}
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle"></i> @lang('area_responsibles.sections.basic_info')
                </h5>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>
                        <i class="fas fa-user text-primary"></i>
                        @lang('area_responsibles.attributes.name')
                        <span class="text-danger">*</span>
                    </label>
                    {{ BsForm::text('name')
                        ->placeholder(trans('area_responsibles.placeholders.name'))
                        ->autofocus()
                        ->required()
                        ->label(false) }}
                </div>
            </div>
        </div>
    </div>

    {{-- بيانات الاتصال --}}
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-address-card"></i> @lang('area_responsibles.sections.contact_info')
                </h5>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>
                        <i class="fas fa-phone text-success"></i>
                        @lang('area_responsibles.attributes.phone')
                    </label>
                    {{ BsForm::text('phone')
                        ->placeholder(trans('area_responsibles.placeholders.phone'))
                        ->label(false) }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- العنوان --}}
<div class="card mb-4">
    <div class="card-header bg-gray text-dark">
        <h5 class="mb-0">
            <i class="fas fa-map-marked-alt"></i> @lang('area_responsibles.sections.address')
        </h5>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label>
                <i class="fas fa-map-marker-alt text-info"></i>
                @lang('area_responsibles.attributes.address')
            </label>
            {{ BsForm::textarea('address')
                ->placeholder(trans('area_responsibles.placeholders.address'))
                ->attribute('rows', '3')
                ->label(false) }}
        </div>
    </div>
</div>
