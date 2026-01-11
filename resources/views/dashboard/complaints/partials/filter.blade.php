{{ BsForm::resource('complaints')->get(url()->current()) }}

@component('dashboard::components.box')
    @slot('title')
        <i class="fas fa-filter"></i> @lang('complaints.filter')
    @endslot

    <div class="row">
        {{-- البحث والإعدادات --}}
        <div class="col-12 mb-1 mt-1">
            <h6 class="text-primary mb-0">
                <i class="fas fa-search"></i> @lang('complaints.sections.search_settings')
            </h6>
            <hr class="mt-1 mb-2">
        </div>

        <div class="col-md-6 mb-2">
            <label class="mb-1">
                <i class="fas fa-id-card"></i> @lang('complaints.attributes.id_num')
            </label>
            {{ BsForm::text('id_num')
                ->value(request('id_num'))
                ->placeholder(trans('complaints.placeholders.id_num'))
                ->label(false) }}
        </div>
        <div class="col-md-6 mb-2">
            <label class="mb-1">
                <i class="fas fa-list-ol"></i> @lang('complaints.perPage')
            </label>
            {{ BsForm::number('perPage')
                ->value(request('perPage', 15))
                ->min(1)
                ->label(false) }}
        </div>
    </div>

    @slot('footer')
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-filter"></i>
            @lang('complaints.actions.filter')
        </button>

        <button type="button" class="btn btn-secondary ml-2" id="resetFilters">
            <i class="fas fa-eraser"></i>
            @lang('complaints.actions.reset')
        </button>
    @endslot
@endcomponent

@push('scripts')
<script>
$(document).ready(function() {
    // زر إعادة تعيين الفلاتر
    $('#resetFilters').on('click', function() {
        $('form')[0].reset();
        history.pushState({}, document.title, window.location.pathname);
        location.reload();
    });
});
</script>
@endpush

{{ BsForm::close() }}
