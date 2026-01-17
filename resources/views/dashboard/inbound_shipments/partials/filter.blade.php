{{ BsForm::resource('inbound_shipments')->get(url()->current()) }}

@component('dashboard::components.box')
    @slot('title')
        <i class="fas fa-filter"></i> @lang('inbound_shipments.filter')
    @endslot

    <div class="row">
        <div class="col-md-6 mb-2">
            <label class="mb-1 font-weight-bold">
                <i class="fas fa-tag text-primary"></i>
                @lang('inbound_shipments.attributes.name')
            </label>
            {{ BsForm::text('name')
                ->value(request('name'))
                ->placeholder(trans('inbound_shipments.placeholders.name'))
                ->label(false) }}
        </div>

        <div class="col-md-6 mb-2">
            <label class="mb-1 font-weight-bold">
                <i class="fas fa-list-ol text-info"></i>
                @lang('inbound_shipments.perPage')
            </label>
            {{ BsForm::number('perPage')
                ->value(request('perPage', 15))
                ->min(1)
                ->label(false) }}
        </div>
    </div>

    @slot('footer')
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa-filter"></i>
            @lang('inbound_shipments.actions.filter')
        </button>

        <button type="button" class="btn btn-secondary btn-sm {{ app()->isLocale('ar') ? 'mr-2' : 'ml-2' }}" id="resetFiltersInboundShipments">
            <i class="fas fa-eraser"></i>
            @lang('inbound_shipments.actions.reset')
        </button>
    @endslot
@endcomponent

@push('scripts')
<script>
$(document).ready(function() {
    $('#resetFiltersInboundShipments').on('click', function() {
        $('form')[0].reset();
        history.pushState({}, document.title, window.location.pathname);
        location.reload();
    });
});
</script>
@endpush

{{ BsForm::close() }}
