@include('dashboard.errors')

{{ BsForm::select('project_id')->options($projects->pluck('name', 'id'))->label(trans('need_requests.attributes.project_id'))->placeholder(trans('need_requests.select')) }}


@if(isset($projectSetting) && $projectSetting->allowed_id_count)
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        الحد الأقصى المسموح به لهذا المشروع: <strong>{{ $projectSetting->allowed_id_count }}</strong> هويات.
    </div>
@endif

@if(isset($need_request))
    @if($need_request->isPending())
        {{ BsForm::textarea('person_ids')->value($need_request->person_ids)->label(trans('need_requests.attributes.person_ids'))->placeholder("أدخل أرقام الهويات هنا، رقم في كل سطر...") }}
    @endif
@else
    {{ BsForm::textarea('person_ids')->label(trans('need_requests.attributes.person_ids'))->placeholder("أدخل أرقام الهويات هنا، رقم في كل سطر...") }}
@endif

{{ BsForm::textarea('notes')->label(trans('need_requests.attributes.notes')) }}

