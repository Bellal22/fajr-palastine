<x-layout :title="$block->name" :breadcrumbs="['dashboard.blocks.show', $block]">

    <div class="row">
        {{-- Profile Card --}}
        <div class="col-lg-4 col-md-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-4">
                        <div class="bg-primary rounded-circle d-inline-flex p-4 shadow">
                            <i class="fas fa-walking fa-4x text-white"></i>
                        </div>
                    </div>
                    <h3 class="mb-2 font-weight-bold">{{ $block->name }}</h3>
                    <p class="text-muted mb-3">
                        <i class="fas fa-user-tie"></i> {{ $block->title }}
                    </p>
                    <div class="mb-3">
                        <span class="badge badge-primary badge-pill px-3 py-2">
                            <i class="fas fa-user-friends"></i>
                            {{ $block->people_count }} @lang('blocks.person')
                        </span>
                    </div>
                    <hr>
                    <div class="text-left">
                        <p class="mb-2">
                            <i class="fas fa-phone text-success"></i>
                            <a href="tel:{{ $block->phone }}" class="text-decoration-none ml-2">
                                {{ $block->phone }}
                            </a>
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-user-shield text-info"></i>
                            <span class="ml-2">{{ $block->areaResponsible?->name ?? '-' }}</span>
                        </p>
                    </div>
                </div>
                <div class="card-footer text-center">
                    @include('dashboard.blocks.partials.actions.edit')
                    @include('dashboard.blocks.partials.actions.delete')
                </div>
            </div>
        </div>

        {{-- Details Card --}}
        <div class="col-lg-8 col-md-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i> @lang('blocks.sections.details')
                    </h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-middle mb-0">
                        <tbody>
                            <tr>
                                <th width="200">
                                    <i class="fas fa-users text-primary"></i>
                                    @lang('blocks.attributes.name')
                                </th>
                                <td class="font-weight-bold">{{ $block->name }}</td>
                            </tr>
                            <tr>
                                <th>
                                    <i class="fas fa-user-shield text-info"></i>
                                    @lang('blocks.attributes.area_responsible')
                                </th>
                                <td>
                                    @if($block->areaResponsible)
                                        <span class="badge badge-info">
                                            {{ $block->areaResponsible->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <i class="fas fa-user-tie text-success"></i>
                                    @lang('blocks.attributes.title')
                                </th>
                                <td>{{ $block->title }}</td>
                            </tr>
                            <tr>
                                <th>
                                    <i class="fas fa-phone text-success"></i>
                                    @lang('blocks.attributes.phone')
                                </th>
                                <td>
                                    <a href="tel:{{ $block->phone }}" class="text-decoration-none">
                                        <i class="fas fa-phone-square"></i> {{ $block->phone }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <i class="fas fa-user-friends text-primary"></i>
                                    @lang('blocks.attributes.people_count')
                                </th>
                                <td>
                                    <span class="badge badge-primary badge-pill">
                                        {{ $block->people_count }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Location Card --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-success text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-map-marked-alt"></i> @lang('blocks.sections.location')
                    </h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-middle mb-0">
                        <tbody>
                            <tr>
                                <th width="200">
                                    <i class="fas fa-map-pin text-danger"></i>
                                    @lang('blocks.attributes.lat')
                                </th>
                                <td>{{ $block->lat }}</td>
                            </tr>
                            <tr>
                                <th>
                                    <i class="fas fa-map-pin text-danger"></i>
                                    @lang('blocks.attributes.lan')
                                </th>
                                <td>{{ $block->lan }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-center">
                    <a href="https://www.google.com/maps?q={{ $block->lat }},{{ $block->lan }}"
                       target="_blank"
                       class="btn btn-primary">
                        <i class="fas fa-map-marker-alt"></i>
                        @lang('blocks.actions.view_on_map')
                    </a>
                </div>
            </div>

            {{-- Notes Card --}}
            @if($block->note)
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-sticky-note"></i> @lang('blocks.sections.notes')
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $block->note }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-layout>
