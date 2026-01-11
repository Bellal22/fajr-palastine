@include('dashboard.errors')

{{ BsForm::text('name')->label(trans('suppliers.attributes.name')) }}

{{ BsForm::textarea('description')->label(trans('suppliers.attributes.description')) }}

{{ BsForm::select('type')
    ->label(trans('suppliers.attributes.type'))
    ->options([
        'donor' => 'جهة مانحة',
        'operator' => 'جهة مشغلة',
    ])
    ->placeholder('-- اختر النوع --')
    ->required()
}}

@isset($supplier)
    {{ BsForm::image('image')->collection('default')->files($supplier->getMediaResource('default')) }}
    {{ BsForm::image('document')->collection('document')->files($supplier->getMediaResource('document')) }}
@else
    {{ BsForm::image('image')->collection('default') }}
    {{ BsForm::image('document')->collection('document') }}
@endisset
