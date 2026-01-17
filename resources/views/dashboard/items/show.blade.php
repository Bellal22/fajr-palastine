<x-layout :title="trans('items.show.title', ['name' => $item->name])" :breadcrumbs="['dashboard.items.index', 'dashboard.items.show']">

    <div class="row">
        {{-- Profile Card --}}
        <div class="col-lg-4 col-md-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-4">
                        <div class="rounded-circle d-inline-flex p-4 shadow"
                             style="background-color: {{ $item->package ? '#28a745' : '#17a2b8' }}20;">
                            <i class="fas fa-cubes fa-4x"
                               style="color: {{ $item->package ? '#28a745' : '#17a2b8' }};"></i>
                        </div>
                    </div>
                    <h3 class="mb-2 font-weight-bold">{{ $item->name }}</h3>
                    <p class="text-muted mb-3">
                        <i class="fas fa-cube"></i> @lang('items.singular')
                    </p>
                    <div class="mb-3">
                        @if($item->package)
                            <span class="badge badge-success badge-pill px-3 py-2">
                                <i class="fas fa-box"></i>
                                @lang('items.types.package')
                            </span>
                        @else
                            <span class="badge badge-info badge-pill px-3 py-2">
                                <i class="fas fa-cube"></i>
                                @lang('items.types.single')
                            </span>
                        @endif
                    </div>
                    <hr>
                    <div class="text-left">
                        <p class="mb-2">
                            <i class="fas fa-calendar-plus text-primary"></i>
                            <span class="{{ app()->isLocale('ar') ? 'mr-2' : 'ml-2' }}">@lang('items.attributes.created_at'):</span>
                            <br>
                            <small class="text-muted {{ app()->isLocale('ar') ? 'mr-4' : 'ml-4' }}">
                                {{ $item->created_at->format('Y-m-d H:i') }}
                            </small>
                        </p>
                        @if($item->updated_at && $item->updated_at != $item->created_at)
                            <p class="mb-0">
                                <i class="fas fa-calendar-check text-info"></i>
                                <span class="{{ app()->isLocale('ar') ? 'mr-2' : 'ml-2' }}">@lang('items.attributes.updated_at'):</span>
                                <br>
                                <small class="text-muted {{ app()->isLocale('ar') ? 'mr-4' : 'ml-4' }}">
                                    {{ $item->updated_at->format('Y-m-d H:i') }}
                                </small>
                            </p>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-light p-3">
                    {{-- Action Buttons Group --}}
                    <div class="btn-group w-100 mb-2" role="group">
                        @can('update', $item)
                            <a href="{{ route('dashboard.items.edit', $item) }}"
                               class="btn btn-primary"
                               title="@lang('items.actions.edit')">
                                <i class="fas fa-edit"></i> @lang('items.actions.edit')
                            </a>
                        @endcan

                        @can('delete', $item)
                            <button type="button"
                                    class="btn btn-danger"
                                    onclick="deleteItem({{ $item->id }})"
                                    title="@lang('items.actions.delete')">
                                <i class="fas fa-trash"></i> @lang('items.actions.delete')
                            </button>

                            <form id="delete-form-{{ $item->id }}"
                                  action="{{ route('dashboard.items.destroy', $item) }}"
                                  method="POST"
                                  style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-lg-8 col-md-12">

            {{-- Item Info Card --}}
            <div class="card border-primary mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i> @lang('items.sections.item_info')
                    </h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-middle mb-0">
                        <tbody>
                            <tr>
                                <th width="200">
                                    <i class="fas fa-tag text-primary"></i>
                                    @lang('items.attributes.name')
                                </th>
                                <td>
                                    <strong class="text-primary">{{ $item->name }}</strong>
                                </td>
                            </tr>

                            <tr>
                                <th>
                                    <i class="fas fa-align-right text-secondary"></i>
                                    @lang('items.attributes.description')
                                </th>
                                <td>
                                    @if($item->description)
                                        {{ $item->description }}
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-minus-circle"></i>
                                            @lang('items.messages.no_description')
                                        </span>
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <th>
                                    <i class="fas fa-truck-loading text-info"></i>
                                    @lang('items.attributes.inbound_shipment_id')
                                </th>
                                <td>
                                    @if($item->inboundShipment)
                                        <a href="{{ route('dashboard.inbound_shipments.show', $item->inboundShipment) }}"
                                           class="text-decoration-none">
                                            <span class="badge badge-info badge-pill">
                                                <i class="fas fa-external-link-alt"></i>
                                                {{ $item->inboundShipment->shipment_number }}
                                            </span>
                                        </a>
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-minus-circle"></i>
                                            @lang('items.messages.no_shipment')
                                        </span>
                                    @endif
                                </td>
                            </tr>

                            @if($item->type !== null)
                                <tr>
                                    <th>
                                        <i class="fas fa-layer-group text-warning"></i>
                                        @lang('items.attributes.type')
                                    </th>
                                    <td>
                                        <span class="badge badge-warning">{{ $item->type }}</span>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Statistics Card --}}
            <div class="card border-info mb-4 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar"></i> @lang('items.sections.statistics')
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="p-3 border rounded">
                                <i class="fas fa-sort-numeric-up fa-2x text-primary mb-2"></i>
                                <h6 class="mb-1">@lang('items.attributes.quantity')</h6>
                                <h4 class="mb-0 text-primary">{{ $item->quantity }}</h4>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="p-3 border rounded">
                                <i class="fas fa-weight fa-2x text-success mb-2"></i>
                                <h6 class="mb-1">@lang('items.attributes.weight')</h6>
                                <h4 class="mb-0 text-success">
                                    @if($item->weight)
                                        {{ number_format($item->weight, 2) }} @lang('items.units.kg')
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </h4>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-3 border rounded">
                                <i class="fas fa-weight-hanging fa-2x text-warning mb-2"></i>
                                <h6 class="mb-1">@lang('items.statistics.total_weight')</h6>
                                <h4 class="mb-0 text-warning">
                                    {{ number_format($item->quantity * ($item->weight ?? 0), 2) }} @lang('items.units.kg')
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    <div class="row">
                        <div class="col-md-6">
                            <small>
                                <i class="fas fa-calendar-plus"></i>
                                @lang('items.show.created_date'):
                                <strong>{{ $item->created_at->format('Y-m-d') }}</strong>
                                <span class="text-muted">({{ $item->created_at->diffForHumans() }})</span>
                            </small>
                        </div>
                        @if($item->updated_at && $item->updated_at != $item->created_at)
                            <div class="col-md-6 text-md-right">
                                <small>
                                    <i class="fas fa-calendar-check"></i>
                                    @lang('items.show.last_update'):
                                    <strong>{{ $item->updated_at->format('Y-m-d') }}</strong>
                                    <span class="text-muted">({{ $item->updated_at->diffForHumans() }})</span>
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        function deleteItem(id) {
            if (confirm('@lang('items.dialogs.delete')')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
    @endpush

</x-layout>
