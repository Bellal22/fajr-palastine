{{ BsForm::resource('locations')->get(url()->current()) }}

@component('dashboard::components.box')
    @slot('title')
        <i class="fas fa-filter"></i> @lang('locations.filter')
    @endslot

    <div class="row">
        {{-- البحث والإعدادات --}}
        <div class="col-12 mb-1 mt-1">
            <h6 class="text-primary mb-0">
                <i class="fas fa-search"></i> @lang('locations.sections.search_settings')
            </h6>
            <hr class="mt-1 mb-2">
        </div>

        <div class="col-md-6 mb-2">
            <label class="mb-1">
                <i class="fas fa-map-pin"></i> @lang('locations.attributes.name')
            </label>
            {{ BsForm::text('name')
                ->value(request('name'))
                ->placeholder(trans('locations.placeholders.filter_name'))
                ->label(false) }}
        </div>

        <div class="col-md-6 mb-2">
            <label class="mb-1">
                <i class="fas fa-list-ol"></i> @lang('locations.perPage')
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
            @lang('locations.actions.filter')
        </button>

        <button type="button" class="btn btn-secondary {{ app()->isLocale('ar') ? 'mr-2' : 'ml-2' }}" id="resetFilters">
            <i class="fas fa-eraser"></i>
            @lang('locations.actions.reset')
        </button>
    @endslot
@endcomponent

@push('scripts')
<script>
$(document).ready(function() {
    $('#resetFilters').on('click', function() {
        $('form')[0].reset();
        history.pushState({}, document.title, window.location.pathname);
        location.reload();
    });
});
</script>
@endpush

{{ BsForm::close() }}
