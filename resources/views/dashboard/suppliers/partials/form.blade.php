@include('dashboard.errors')

{{-- المعلومات الأساسية - أزرق --}}
<div class="card border-primary mb-3">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-info-circle"></i> @lang('suppliers.sections.basic_info')
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="mb-1 font-weight-bold">
                    <i class="fas fa-tag text-primary"></i> @lang('suppliers.attributes.name')
                </label>
                {{ BsForm::text('name')
                    ->placeholder(trans('suppliers.placeholders.name'))
                    ->label(false) }}
            </div>

            <div class="col-md-12 mb-3">
                <label class="mb-1 font-weight-bold">
                    <i class="fas fa-layer-group text-success"></i> @lang('suppliers.attributes.type')
                </label>
                {{ BsForm::select('type')
                    ->options([
                        'donor' => trans('suppliers.types.donor'),
                        'operator' => trans('suppliers.types.operator'),
                    ])
                    ->placeholder(trans('suppliers.placeholders.select_type'))
                    ->required()
                    ->label(false) }}
            </div>

            <div class="col-md-12 mb-3">
                <label class="mb-1 font-weight-bold">
                    <i class="fas fa-align-right text-secondary"></i> @lang('suppliers.attributes.description')
                </label>
                {{ BsForm::textarea('description')
                    ->placeholder(trans('suppliers.placeholders.description'))
                    ->rows(4)
                    ->label(false) }}
            </div>
        </div>
    </div>
</div>

{{-- المرفقات - أخضر --}}
<div class="card border-success mb-3">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">
            <i class="fas fa-paperclip"></i> @lang('suppliers.sections.attachments')
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="mb-1 font-weight-bold">
                    <i class="fas fa-image text-warning"></i> @lang('suppliers.attributes.image')
                </label>
                @isset($supplier)
                    {{ BsForm::image('image')->collection('default')->files($supplier->getMediaResource('default'))->label(false) }}
                @else
                    {{ BsForm::image('image')->collection('default')->label(false) }}
                @endisset
            </div>

            <div class="col-md-6 mb-3">
                <label class="mb-1 font-weight-bold">
                    <i class="fas fa-file-alt text-danger"></i> @lang('suppliers.attributes.document')
                </label>
                @isset($supplier)
                    {{ BsForm::image('document')->collection('document')->files($supplier->getMediaResource('document'))->label(false) }}
                @else
                    {{ BsForm::image('document')->collection('document')->label(false) }}
                @endisset
            </div>
        </div>
    </div>
</div>
