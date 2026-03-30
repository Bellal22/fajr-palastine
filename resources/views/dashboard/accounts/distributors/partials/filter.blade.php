{{ BsForm::resource('distributors')->get(url()->current()) }}
@component('dashboard::components.box')
    @slot('title')
        <i class="fas fa-search"></i> @lang('distributors.actions.filter')
    @endslot

    <div class="row">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="form-group">
                <label>
                    <i class="fas fa-user"></i> @lang('distributors.attributes.name')
                </label>
                {{ BsForm::text('name')
                    ->placeholder(trans('distributors.filter_placeholders.name'))
                    ->value(request('name'))
                    ->label(false) }}
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="form-group">
                <label>
                    <i class="fas fa-envelope"></i> @lang('distributors.attributes.email')
                </label>
                {{ BsForm::email('email')
                    ->placeholder(trans('distributors.filter_placeholders.email'))
                    ->value(request('email'))
                    ->label(false) }}
            </div>
        </div>

        <div class="col-lg-2 col-md-6 mb-3">
            <div class="form-group">
                <label>
                    <i class="fas fa-phone"></i> @lang('distributors.attributes.phone')
                </label>
                {{ BsForm::text('phone')
                    ->placeholder(trans('distributors.filter_placeholders.phone'))
                    ->value(request('phone'))
                    ->label(false) }}
            </div>
        </div>

        <div class="col-lg-2 col-md-6 mb-3">
            <div class="form-group">
                <label>
                    <i class="fas fa-list-ol"></i> @lang('distributors.perPage')
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
                    @lang('distributors.actions.filter')
                </button>

                <button type="reset" class="btn btn-secondary ml-2">
                    <i class="fas fa-eraser"></i>
                    @lang('distributors.actions.reset_filter')
                </button>
            </div>

            @if(request()->hasAny(['name', 'email', 'phone']))
                <a href="{{ url()->current() }}" class="btn btn-outline-secondary">
                    <i class="fas fa-redo"></i>
                    @lang('distributors.actions.clear_filter')
                </a>
            @endif
        </div>
    @endslot
@endcomponent
{{ BsForm::close() }}
