{{ BsForm::resource('complaint')->get(url()->current()) }}
@component('dashboard::components.box')
    @slot('title', trans('complaint.filter'))

    <div class="row">
        <div class="col-md-6">
            {{ BsForm::text('id_num')->value(request('id_num')) }}
        </div>

        <div class="col-md-6">
            {{ BsForm::number('perPage')
                ->value(request('perPage', 15))
                ->min(1)
                 ->label(trans('complaint.perPage')) }}
        </div>
    </div>

    @slot('footer')
    <button type="submit" class="btn btn-primary btn-sm">
        <i class="fas fa fa-fw fa-filter"></i>
        @lang('complaint.actions.filter')
    </button>

    <button type="button" class="btn btn-secondary btn-sm" id="resetFilters">
        <i class="fas fa fa-fw fa-times"></i>
       @lang('complaint.actions.empty_filters')
    </button>
@endSlot

@endcomponent
@push('scripts')
    <script>
        document.getElementById('resetFilters').addEventListener('click', function () {
            let form = this.closest('form'); // البحث عن الفورم الذي يحتوي على الزر
            form.reset(); // مسح كل المدخلات
        });
    </script>
@endpush
{{ BsForm::close() }}
