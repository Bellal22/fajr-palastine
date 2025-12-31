{{ BsForm::resource('blocks')->get(url()->current()) }}
@component('dashboard::components.box')
    @slot('title', trans('blocks.filter'))

    <div class="row">
        <div class="col-md-4">
            {{ BsForm::text('name')
                ->label(trans('blocks.name'))
                ->value(request('name'))
                ->placeholder(trans('blocks.search_by_name')) }}
        </div>

        <div class="col-md-4">
            {{ BsForm::select('area_responsible_id')
                ->label(trans('blocks.area_responsible'))
                ->options(
                    \App\Models\AreaResponsible::pluck('name', 'id')->toArray()
                )
                ->placeholder(trans('blocks.select_area_responsible'))
                ->value(request('area_responsible_id')) }}
        </div>

        <div class="col-md-4">
            {{ BsForm::number('perPage')
                ->value(request('perPage', 15))
                ->min(1)
                ->label(trans('blocks.perPage')) }}
        </div>
    </div>

    @slot('footer')
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa-filter"></i>
            @lang('blocks.actions.filter')
        </button>
        <a href="{{ url()->current() }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-redo"></i>
            @lang('blocks.actions.reset')
        </a>
    @endslot
@endcomponent
{{ BsForm::close() }}
