@include('dashboard.errors')

{{ BsForm::text('name') }}
{{ BsForm::textarea('description') }}

@isset($supplier)
    {{ BsForm::image('image')->files($supplier->getMediaResource()) }}
    {{ BsForm::file('document')->files($supplier->getMediaResource('document')) }}
@else
    {{ BsForm::image('image') }}
    {{ BsForm::file('document') }}
@endisset

