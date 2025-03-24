{{ BsForm::resource('people')->get(url()->current()) }}
@component('dashboard::components.box')
    @slot('title', trans('people.filter'))

    <div class="row">
        <div class="col-md-6">
            {{ BsForm::text('name')->value(request('name')) }}
        </div>
        <div class="col-md-6">
            {{ BsForm::text('social_status')->value(request('social_status')) }}
        </div>
        <div class="col-md-6">
            {{ BsForm::number('perPage')
                ->value(request('perPage', 15))
                ->min(1)
                 ->label(trans('people.perPage')) }}
        </div>
    </div>

    @slot('footer')
    <button type="submit" class="btn btn-primary btn-sm">
        <i class="fas fa fa-fw fa-filter"></i>
        @lang('people.actions.filter')
    </button>

    <button type="button" class="btn btn-secondary btn-sm" id="resetFilters">
        <i class="fas fa fa-fw fa-times"></i>
       @lang('people.actions.empty_filters')
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
