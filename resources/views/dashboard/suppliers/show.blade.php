<x-layout :title="$supplier->name" :breadcrumbs="['dashboard.suppliers.index', 'dashboard.suppliers.show']">

    <div class="row">
        {{-- Profile Card (Basic Info) --}}
        <div class="col-lg-4 col-md-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-4">
                        @if($supplier->getFirstMediaUrl('default'))
                            <img src="{{ $supplier->getFirstMediaUrl('default') }}"
                                 alt="{{ $supplier->name }}"
                                 class="rounded-circle shadow"
                                 style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #ddd;">
                        @else
                            <div class="rounded-circle d-inline-flex p-4 shadow"
                                 style="background-color: #f0f0f0;">
                                <i class="fas fa-building fa-4x text-muted"></i>
                            </div>
                        @endif
                    </div>
                    <h3 class="mb-2 font-weight-bold">{{ $supplier->name }}</h3>
                    <p class="text-muted mb-3">
                        <i class="fas fa-handshake"></i> @lang('suppliers.singular')
                    </p>
                    <div class="mb-3">
                        @if($supplier->type == 'donor')
                            <span class="badge badge-info badge-pill px-3 py-2">
                                <i class="fas fa-hand-holding-heart"></i>
                                @lang('suppliers.types.donor')
                            </span>
                        @elseif($supplier->type == 'operator')
                            <span class="badge badge-success badge-pill px-3 py-2">
                                <i class="fas fa-cogs"></i>
                                @lang('suppliers.types.operator')
                            </span>
                        @endif
                    </div>
                    <hr>
                    <div class="text-left">
                        <p class="mb-2">
                            <i class="fas fa-calendar-plus text-primary"></i>
                            <span class="{{ app()->isLocale('ar') ? 'mr-2' : 'ml-2' }}">@lang('suppliers.attributes.created_at'):</span>
                            <br>
                            <small class="text-muted {{ app()->isLocale('ar') ? 'mr-4' : 'ml-4' }}">
                                {{ $supplier->created_at->format('Y-m-d H:i') }}
                            </small>
                        </p>
                        @if($supplier->updated_at && $supplier->updated_at != $supplier->created_at)
                            <p class="mb-0">
                                <i class="fas fa-calendar-check text-info"></i>
                                <span class="{{ app()->isLocale('ar') ? 'mr-2' : 'ml-2' }}">@lang('suppliers.attributes.updated_at'):</span>
                                <br>
                                <small class="text-muted {{ app()->isLocale('ar') ? 'mr-4' : 'ml-4' }}">
                                    {{ $supplier->updated_at->format('Y-m-d H:i') }}
                                </small>
                            </p>
                        @endif
                    </div>
                </div>
                <div class="card-footer text-center">
                    <div class="btn-group" role="group">
                        @include('dashboard.suppliers.partials.actions.edit')
                        @include('dashboard.suppliers.partials.actions.delete')
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-lg-8 col-md-12">

            {{-- Description Card --}}
            @if($supplier->description)
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-align-right"></i> @lang('suppliers.attributes.description')
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $supplier->description }}</p>
                    </div>
                </div>
            @endif

            {{-- Attachments Card --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-paperclip"></i> @lang('suppliers.sections.attachments')
                    </h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-middle mb-0">
                        <tbody>
                            <tr>
                                <th width="200">
                                    <i class="fas fa-image text-warning"></i>
                                    @lang('suppliers.attributes.image')
                                </th>
                                <td>
                                    @if($supplier->hasMedia('default'))
                                        <div class="mb-2">
                                            <a href="{{ $supplier->getFirstMediaUrl('default') }}"
                                               target="_blank"
                                               data-toggle="lightbox">
                                                <img src="{{ $supplier->getFirstMediaUrl('default') }}"
                                                     alt="{{ $supplier->name }}"
                                                     style="max-width: 200px; max-height: 200px;"
                                                     class="img-thumbnail">
                                            </a>
                                        </div>
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle"></i>
                                            @lang('suppliers.status.has_image')
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-times-circle"></i>
                                            @lang('suppliers.status.no_image')
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <i class="fas fa-file-alt text-danger"></i>
                                    @lang('suppliers.attributes.document')
                                </th>
                                <td>
                                    @if($supplier->hasMedia('document'))
                                        <div class="mb-2">
                                            <a href="{{ $supplier->getFirstMediaUrl('document') }}"
                                               target="_blank"
                                               class="btn btn-sm btn-primary"
                                               download>
                                                <i class="fas fa-file-download"></i>
                                                @lang('suppliers.actions.download_document')
                                            </a>
                                            <a href="{{ $supplier->getFirstMediaUrl('document') }}"
                                               target="_blank"
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                                @lang('suppliers.actions.view_document')
                                            </a>
                                        </div>
                                        <small class="text-muted d-block mb-2">
                                            <i class="fas fa-file"></i>
                                            {{ basename($supplier->getFirstMediaUrl('document')) }}
                                        </small>
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle"></i>
                                            @lang('suppliers.status.has_document')
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-times-circle"></i>
                                            @lang('suppliers.status.no_document')
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Statistics Card --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar"></i> @lang('suppliers.sections.statistics')
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="p-3 border rounded">
                                <i class="fas fa-layer-group fa-2x text-primary mb-2"></i>
                                <h6 class="mb-1">@lang('suppliers.attributes.type')</h6>
                                @if($supplier->type == 'donor')
                                    <span class="badge badge-info">@lang('suppliers.types.donor')</span>
                                @elseif($supplier->type == 'operator')
                                    <span class="badge badge-success">@lang('suppliers.types.operator')</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="p-3 border rounded">
                                <i class="fas fa-image fa-2x text-warning mb-2"></i>
                                <h6 class="mb-1">@lang('suppliers.attributes.image')</h6>
                                @if($supplier->hasMedia('default'))
                                    <span class="badge badge-success">@lang('suppliers.status.has_image')</span>
                                @else
                                    <span class="badge badge-secondary">@lang('suppliers.status.no_image')</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded">
                                <i class="fas fa-file-alt fa-2x text-danger mb-2"></i>
                                <h6 class="mb-1">@lang('suppliers.attributes.document')</h6>
                                @if($supplier->hasMedia('document'))
                                    <span class="badge badge-success">@lang('suppliers.status.has_document')</span>
                                @else
                                    <span class="badge badge-secondary">@lang('suppliers.status.no_document')</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    <div class="row">
                        <div class="col-md-6">
                            <small>
                                <i class="fas fa-calendar-plus"></i>
                                @lang('suppliers.show.created_date'):
                                <strong>{{ $supplier->created_at->format('Y-m-d') }}</strong>
                                <span class="text-muted">({{ $supplier->created_at->diffForHumans() }})</span>
                            </small>
                        </div>
                        @if($supplier->updated_at && $supplier->updated_at != $supplier->created_at)
                            <div class="col-md-6 text-md-right">
                                <small>
                                    <i class="fas fa-calendar-check"></i>
                                    @lang('suppliers.show.last_update'):
                                    <strong>{{ $supplier->updated_at->format('Y-m-d') }}</strong>
                                    <span class="text-muted">({{ $supplier->updated_at->diffForHumans() }})</span>
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

</x-layout>
