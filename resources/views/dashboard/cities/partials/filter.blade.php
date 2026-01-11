{{ BsForm::resource('cities')->get(url()->current()) }}
@component('dashboard::components.box')
    @slot('title')
        <i class="fas fa-search"></i> @lang('cities.actions.filter')
    @endslot

    <div class="row">
        <div class="col-lg-10 col-md-8 mb-3">
            <div class="form-group">
                <label>
                    <i class="fas fa-city"></i> @lang('cities.attributes.name')
                </label>
                {{ BsForm::text('name')
                    ->placeholder(trans('cities.filter_placeholders.name'))
                    ->value(request('name'))
                    ->label(false) }}
            </div>
        </div>

        <div class="col-lg-2 col-md-4 mb-3">
            <div class="form-group">
                <label>
                    <i class="fas fa-list-ol"></i> @lang('cities.perPage')
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
                    @lang('cities.actions.filter')
                </button>

                <button type="reset" class="btn btn-secondary ml-2">
                    <i class="fas fa-eraser"></i>
                    @lang('cities.actions.reset_filter')
                </button>
            </div>

            @if(request()->hasAny(['name']))
                <a href="{{ url()->current() }}" class="btn btn-outline-secondary">
                    <i class="fas fa-redo"></i>
                    @lang('cities.actions.clear_filter')
                </a>
            @endif
        </div>
    @endslot
@endcomponent
{{ BsForm::close() }}
