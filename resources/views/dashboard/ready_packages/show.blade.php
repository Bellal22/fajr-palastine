<x-layout :title="trans('ready_packages.show.title', ['name' => $ready_package->name])" :breadcrumbs="['dashboard.ready_packages.index', 'dashboard.ready_packages.show']">

    <div class="row">
        {{-- Profile Card --}}
        <div class="col-lg-4 col-md-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-4">
                        <div class="rounded-circle d-inline-flex p-4 shadow"
                             style="background-color: #28a74520;">
                            <i class="fas fa-box fa-4x text-success"></i>
                        </div>
                    </div>
                    <h3 class="mb-2 font-weight-bold">{{ $ready_package->name }}</h3>
                    <p class="text-muted mb-3">
                        <i class="fas fa-box"></i> @lang('ready_packages.singular')
                    </p>
                    <div class="mb-3">
                        <span class="badge badge-success badge-pill px-3 py-2">
                            <i class="fas fa-box"></i>
                            @lang('ready_packages.types.ready_package')
                        </span>
                    </div>
                    <hr>
                    <div class="text-left">
                        <p class="mb-2">
                            <i class="fas fa-calendar-plus text-primary"></i>
                            <span class="{{ app()->isLocale('ar') ? 'mr-2' : 'ml-2' }}">@lang('ready_packages.attributes.created_at'):</span>
                            <br>
                            <small class="text-muted {{ app()->isLocale('ar') ? 'mr-4' : 'ml-4' }}">
                                {{ $ready_package->created_at->format('Y-m-d H:i') }}
                            </small>
                        </p>
                        @if($ready_package->updated_at && $ready_package->updated_at != $ready_package->created_at)
                            <p class="mb-0">
                                <i class="fas fa-calendar-check text-info"></i>
                                <span class="{{ app()->isLocale('ar') ? 'mr-2' : 'ml-2' }}">@lang('ready_packages.attributes.updated_at'):</span>
                                <br>
                                <small class="text-muted {{ app()->isLocale('ar') ? 'mr-4' : 'ml-4' }}">
                                    {{ $ready_package->updated_at->format('Y-m-d H:i') }}
                                </small>
                            </p>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-light p-3">
                    {{-- Action Buttons Group --}}
                    <div class="btn-group w-100 mb-2" role="group">
                        @can('update', $ready_package)
                            <a href="{{ route('dashboard.ready_packages.edit', $ready_package) }}"
                               class="btn btn-primary"
                               title="@lang('ready_packages.actions.edit')">
                                <i class="fas fa-edit"></i> @lang('ready_packages.actions.edit')
                            </a>
                        @endcan

                        @can('delete', $ready_package)
                            <button type="button"
                                    class="btn btn-danger"
                                    onclick="deleteReadyPackage({{ $ready_package->id }})"
                                    title="@lang('ready_packages.actions.delete')">
                                <i class="fas fa-trash"></i> @lang('ready_packages.actions.delete')
                            </button>

                            <form id="delete-form-{{ $ready_package->id }}"
                                  action="{{ route('dashboard.ready_packages.destroy', $ready_package) }}"
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

            {{-- Package Info Card --}}
            <div class="card border-primary mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i> @lang('ready_packages.sections.package_info')
                    </h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-middle mb-0">
                        <tbody>
                            <tr>
                                <th width="200">
                                    <i class="fas fa-box text-primary"></i>
                                    @lang('ready_packages.attributes.name')
                                </th>
                                <td>
                                    <strong class="text-primary">{{ $ready_package->name }}</strong>
                                </td>
                            </tr>

                            <tr>
                                <th>
                                    <i class="fas fa-align-right text-secondary"></i>
                                    @lang('ready_packages.attributes.description')
                                </th>
                                <td>
                                    @if($ready_package->description)
                                        {{ $ready_package->description }}
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-minus-circle"></i>
                                            @lang('ready_packages.messages.no_description')
                                        </span>
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <th>
                                    <i class="fas fa-truck-loading text-info"></i>
                                    @lang('ready_packages.attributes.inbound_shipment_id')
                                </th>
                                <td>
                                    @if($ready_package->inboundShipment)
                                        <a href="{{ route('dashboard.inbound_shipments.show', $ready_package->inboundShipment) }}"
                                           class="text-decoration-none">
                                            <span class="badge badge-info badge-pill">
                                                <i class="fas fa-external-link-alt"></i>
                                                {{ $ready_package->inboundShipment->shipment_number }}
                                            </span>
                                        </a>
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-minus-circle"></i>
                                            @lang('ready_packages.messages.no_shipment')
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Statistics Card --}}
            <div class="card border-info mb-4 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar"></i> @lang('ready_packages.sections.statistics')
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="p-3 border rounded">
                                <i class="fas fa-sort-numeric-up fa-2x text-primary mb-2"></i>
                                <h6 class="mb-1">@lang('ready_packages.attributes.quantity')</h6>
                                <h4 class="mb-0 text-primary">{{ $ready_package->quantity }}</h4>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="p-3 border rounded">
                                <i class="fas fa-weight fa-2x text-success mb-2"></i>
                                <h6 class="mb-1">@lang('ready_packages.attributes.weight')</h6>
                                <h4 class="mb-0 text-success">
                                    @if($ready_package->weight)
                                        {{ number_format($ready_package->weight, 2) }} @lang('ready_packages.units.kg')
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </h4>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-3 border rounded">
                                <i class="fas fa-weight-hanging fa-2x text-warning mb-2"></i>
                                <h6 class="mb-1">@lang('ready_packages.statistics.total_weight')</h6>
                                <h4 class="mb-0 text-warning">
                                    {{ number_format($ready_package->quantity * ($ready_package->weight ?? 0), 2) }} @lang('ready_packages.units.kg')
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
                                @lang('ready_packages.show.created_date'):
                                <strong>{{ $ready_package->created_at->format('Y-m-d') }}</strong>
                                <span class="text-muted">({{ $ready_package->created_at->diffForHumans() }})</span>
                            </small>
                        </div>
                        @if($ready_package->updated_at && $ready_package->updated_at != $ready_package->created_at)
                            <div class="col-md-6 text-md-right">
                                <small>
                                    <i class="fas fa-calendar-check"></i>
                                    @lang('ready_packages.show.last_update'):
                                    <strong>{{ $ready_package->updated_at->format('Y-m-d') }}</strong>
                                    <span class="text-muted">({{ $ready_package->updated_at->diffForHumans() }})</span>
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Package Contents --}}
    @if($ready_package->items && $ready_package->items->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card border-success mb-4 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-cubes"></i> @lang('ready_packages.sections.package_contents')
                        <span class="badge badge-light">{{ $ready_package->items->count() }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 5%;" class="text-center">#</th>
                                    <th style="width: 30%;">
                                        <i class="fas fa-tag"></i> @lang('ready_packages.table.item_name')
                                    </th>
                                    <th style="width: 40%;">
                                        <i class="fas fa-align-right"></i> @lang('ready_packages.table.description')
                                    </th>
                                    <th style="width: 12%" class="text-center">
                                        <i class="fas fa-sort-numeric-up"></i> @lang('ready_packages.table.quantity')
                                    </th>
                                    <th style="width: 13%" class="text-center">
                                        <i class="fas fa-weight"></i> @lang('ready_packages.table.weight')
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ready_package->items as $index => $item)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td><strong>{{ $item->name }}</strong></td>
                                        <td>{{ $item->description ?? '-' }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-primary badge-pill">{{ $item->quantity }}</span>
                                        </td>
                                        <td class="text-center">
                                            {{ $item->weight ? number_format($item->weight, 2) : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
        function deleteReadyPackage(id) {
            if (confirm('@lang('ready_packages.dialogs.delete')')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
    @endpush

</x-layout>
