@include('dashboard.errors')

{{ BsForm::select('project_id')->options($projects->pluck('name', 'id'))->label(trans('need_requests.attributes.project_id'))->placeholder(trans('need_requests.select')) }}

@if(isset($need_request))
    {{-- In edit mode, we might want to show items differently, but for now just basic notes --}}
@else
    {{ BsForm::textarea('person_ids')->label(trans('need_requests.attributes.person_ids'))->placeholder("أدخل أرقام الهويات هنا، رقم في كل سطر...") }}
@endif

{{ BsForm::textarea('notes')->label(trans('need_requests.attributes.notes')) }}

