<x-layout :title="$area_responsible->name" :breadcrumbs="['dashboard.area_responsibles.show', $area_responsible]">

    <div class="row">
        {{-- Profile Card --}}
        <div class="col-lg-4 col-md-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-4">
                        <div class="bg-primary rounded-circle d-inline-flex p-4 shadow">
                            <i class="fas fa-user-tie fa-4x text-white"></i>
                        </div>
                    </div>
                    <h3 class="mb-2 font-weight-bold">{{ $area_responsible->name }}</h3>
                    <p class="text-muted mb-3">
                        <i class="fas fa-user-tie"></i> @lang('area_responsibles.role')
                    </p>
                    <div class="mb-3">
                        <span class="badge badge-primary badge-pill px-3 py-2">
                            <i class="fas fa-users"></i>
                            {{ $area_responsible->blocks->count() }} @lang('area_responsibles.delegate')
                        </span>
                        <span class="badge badge-success badge-pill px-3 py-2 mt-2">
                            <i class="fas fa-user-check"></i>
                            {{ $area_responsible->people_count ?? 0 }} @lang('area_responsibles.person')
                        </span>
                    </div>
                    <hr>
                    <div class="text-left">
                        <p class="mb-2">
                            <i class="fas fa-phone text-success"></i>
                            <a href="tel:{{ $area_responsible->phone }}" class="text-decoration-none ml-2">
                                {{ $area_responsible->phone ?? '-' }}
                            </a>
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-map-marker-alt text-info"></i>
                            <span class="ml-2">{{ $area_responsible->address ?? '-' }}</span>
                        </p>
                    </div>
                </div>
                <div class="card-footer text-center">
                    @if (auth()->user()?->isAdmin())
                        @include('dashboard.area_responsibles.partials.actions.edit')
                        @include('dashboard.area_responsibles.partials.actions.delete')
                    @endif
                </div>
            </div>
        </div>

        {{-- Details Card --}}
        <div class="col-lg-8 col-md-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i> @lang('area_responsibles.sections.basic_info')
                    </h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-middle mb-0">
                        <tbody>
                            <tr>
                                <th width="200">
                                    <i class="fas fa-user text-primary"></i>
                                    @lang('area_responsibles.attributes.name')
                                </th>
                                <td class="font-weight-bold">{{ $area_responsible->name }}</td>
                            </tr>
                            <tr>
                                <th>
                                    <i class="fas fa-phone text-success"></i>
                                    @lang('area_responsibles.attributes.phone')
                                </th>
                                <td>
                                    @if($area_responsible->phone)
                                        <a href="tel:{{ $area_responsible->phone }}" class="text-decoration-none">
                                            <i class="fas fa-phone-square"></i> {{ $area_responsible->phone }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <i class="fas fa-map-marker-alt text-info"></i>
                                    @lang('area_responsibles.attributes.address')
                                </th>
                                <td>{{ $area_responsible->address ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>
                                    <i class="fas fa-users text-primary"></i>
                                    @lang('area_responsibles.attributes.block_count')
                                </th>
                                <td>
                                    <span class="badge badge-primary badge-pill">
                                        {{ $area_responsible->blocks->count() }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <i class="fas fa-user-check text-success"></i>
                                    @lang('area_responsibles.attributes.person_count')
                                </th>
                                <td>
                                    <span class="badge badge-success badge-pill">
                                        {{ $area_responsible->people_count ?? 0 }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- المندوبين التابعة --}}
            @php
                $blocksInArea = \App\Models\Block::where('area_responsible_id', $area_responsible->id)->paginate(10);
            @endphp

            @if($blocksInArea->total() > 0)
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-users"></i> @lang('area_responsibles.sections.delegates')
                        <span class="badge badge-light badge-pill">{{ $blocksInArea->total() }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th><i class="fas fa-hashtag"></i> #</th>
                                    <th><i class="fas fa-walking"></i> @lang('blocks.attributes.name')</th>
                                    <th><i class="fas fa-user-tie"></i> @lang('blocks.attributes.title')</th>
                                    <th class="d-none d-md-table-cell"><i class="fas fa-phone"></i> @lang('blocks.attributes.phone')</th>
                                    <th class="text-center"><i class="fas fa-user-friends"></i> @lang('blocks.attributes.people_count')</th>
                                    <th class="text-center"><i class="fas fa-cog"></i> @lang('blocks.actions.actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($blocksInArea as $blockItem)
                                    <tr>
                                        <td>
                                            <a href="{{ route('dashboard.blocks.show', $blockItem) }}"
                                               class="text-primary font-weight-bold">
                                                #{{ $blockItem->id }}
                                            </a>
                                        </td>
                                        <td>
                                            <strong>{{ $blockItem->name }}</strong>
                                        </td>
                                        <td>{{ $blockItem->title ?? '-' }}</td>
                                        <td class="d-none d-md-table-cell">
                                            @if($blockItem->phone)
                                                <a href="tel:{{ $blockItem->phone }}" class="text-success text-decoration-none">
                                                    {{ $blockItem->phone }}
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-primary badge-pill">
                                                {{ $blockItem->people_count }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('dashboard.blocks.show', $blockItem) }}"
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> @lang('blocks.actions.show')
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($blocksInArea->hasPages())
                <div class="card-footer">
                    {{ $blocksInArea->links() }}
                </div>
                @endif
            </div>
            @endif

        </div>
    </div>

</x-layout>
