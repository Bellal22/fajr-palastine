@include('dashboard.errors')

{{ BsForm::text('name') }}
{{ BsForm::textarea('description') }}

@isset($supplier)
    {{ BsForm::image('image')->files($supplier->getMediaResource()) }}
    {{ BsForm::image('document')->files($supplier->getMediaResource('document')) }}
@else
    {{ BsForm::image('image') }}
    {{ BsForm::image('document') }}
@endisset

