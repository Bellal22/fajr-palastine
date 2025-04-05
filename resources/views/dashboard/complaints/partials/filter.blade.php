{{ BsForm::resource('complaints')->get(url()->current()) }}
@component('dashboard::components.box')
    @slot('title', trans('complaints.filter'))

    <div class="row">
        <div class="col-md-6">
            {{ BsForm::text('id_num')->value(request('id_num')) }}
        </div>
        <div class="col-md-6">
            {{ BsForm::number('perPage')
                ->value(request('perPage', 15))
                ->min(1)
                 ->label(trans('complaints.perPage')) }}
        </div>
    </div>

    @slot('footer')
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa fa-fw fa-filter"></i>
            @lang('complaints.actions.filter')
        </button>
    @endslot
@endcomponent
{{ BsForm::close() }}
