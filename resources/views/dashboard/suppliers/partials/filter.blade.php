{{ BsForm::resource('suppliers')->get(url()->current()) }}
@component('dashboard::components.box')
    @slot('title', trans('suppliers.filter'))

    <div class="row">
        <div class="col-md-6">
            {{ BsForm::text('name')->value(request('name')) }}
        </div>
        <div class="col-md-6">
            {{ BsForm::number('perPage')
                ->value(request('perPage', 15))
                ->min(1)
                 ->label(trans('suppliers.perPage')) }}
        </div>
    </div>

    @slot('footer')
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa fa-fw fa-filter"></i>
            @lang('suppliers.actions.filter')
        </button>
    @endslot
@endcomponent
{{ BsForm::close() }}
