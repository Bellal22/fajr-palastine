{{ BsForm::resource('supervisors')->get(url()->current()) }}
@component('dashboard::components.box')
    @slot('title')
        <i class="fas fa-search"></i> @lang('supervisors.actions.filter')
    @endslot

    <div class="row">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="form-group">
                <label>
                    <i class="fas fa-user"></i> @lang('supervisors.attributes.name')
                </label>
                {{ BsForm::text('name')
                    ->placeholder(trans('supervisors.filter_placeholders.name'))
                    ->value(request('name'))
                    ->label(false) }}
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="form-group">
                <label>
                    <i class="fas fa-envelope"></i> @lang('supervisors.attributes.email')
                </label>
                {{ BsForm::email('email')
                    ->placeholder(trans('supervisors.filter_placeholders.email'))
                    ->value(request('email'))
                    ->label(false) }}
            </div>
        </div>

        <div class="col-lg-2 col-md-6 mb-3">
            <div class="form-group">
                <label>
                    <i class="fas fa-phone"></i> @lang('supervisors.attributes.phone')
                </label>
                {{ BsForm::text('phone')
                    ->placeholder(trans('supervisors.filter_placeholders.phone'))
                    ->value(request('phone'))
                    ->label(false) }}
            </div>
        </div>

        <div class="col-lg-2 col-md-6 mb-3">
            <div class="form-group">
                <label>
                    <i class="fas fa-toggle-on"></i> @lang('supervisors.attributes.person_status')
                </label>
                {{ BsForm::select('person_status')
                    ->placeholder(trans('supervisors.filter_placeholders.status'))
                    ->options([
                        '' => trans('supervisors.filter_placeholders.all_status'),
                        'فعال' => trans('supervisors.status.active'),
                        'غير فعال' => trans('supervisors.status.inactive'),
                    ])
                    ->value(request('person_status'))
                    ->label(false) }}
            </div>
        </div>

        <div class="col-lg-2 col-md-6 mb-3">
            <div class="form-group">
                <label>
                    <i class="fas fa-list-ol"></i> @lang('supervisors.perPage')
                </label>
                {{ BsForm::number('perPage')
                    ->value(request('perPage', 15))
                    ->min(1)
                    ->max(100)
                    ->label(false) }}
            </div>
        </div>
    </div>

    @slot('footer')
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    @lang('supervisors.actions.filter')
                </button>

                <button type="reset" class="btn btn-secondary ml-2">
                    <i class="fas fa-eraser"></i>
                    @lang('supervisors.actions.reset_filter')
                </button>
            </div>

            @if(request()->hasAny(['name', 'email', 'phone', 'person_status']))
                <a href="{{ url()->current() }}" class="btn btn-outline-secondary">
                    <i class="fas fa-redo"></i>
                    @lang('supervisors.actions.clear_filter')
                </a>
            @endif
        </div>
    @endslot
@endcomponent
{{ BsForm::close() }}
