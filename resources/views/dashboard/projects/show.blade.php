<x-layout :title="$project->name" :breadcrumbs="['dashboard.projects.show', $project]">
    <div class="row">
        <div class="col-md-8">
            @component('dashboard::components.box')
                @slot('title', 'معلومات المشروع')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle mb-0">
                    <tbody>
                    <tr>
                        <th width="200">@lang('projects.attributes.name')</th>
                        <td><strong>{{ $project->name }}</strong></td>
                    </tr>
                    <tr>
                        <th>@lang('projects.attributes.description')</th>
                        <td>{{ $project->description ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>@lang('projects.attributes.status')</th>
                        <td>
                            @if($project->status == 'active')
                                <span class="badge badge-success"><i class="fas fa-check-circle"></i> @lang('projects.status.active')</span>
                            @elseif($project->status == 'completed')
                                <span class="badge badge-primary"><i class="fas fa-flag-checkered"></i> @lang('projects.status.completed')</span>
                            @else
                                <span class="badge badge-warning"><i class="fas fa-pause-circle"></i> @lang('projects.status.suspended')</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('projects.attributes.start_date')</th>
                        <td>
                            @if($project->start_date)
                                <i class="fas fa-calendar-alt text-info"></i> {{ $project->start_date->format('Y-m-d') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('projects.attributes.end_date')</th>
                        <td>
                            @if($project->end_date)
                                <i class="fas fa-calendar-check text-success"></i> {{ $project->end_date->format('Y-m-d') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('projects.attributes.created_at')</th>
                        <td>{{ $project->created_at->format('Y-m-d h:i A') }}</td>
                    </tr>
                    </tbody>
                </table>

                @slot('footer')
                    @include('dashboard.projects.partials.actions.edit')
                    @include('dashboard.projects.partials.actions.delete')
                    @include('dashboard.projects.partials.actions.restore')
                    @include('dashboard.projects.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>

        <div class="col-md-4">
            @component('dashboard::components.box')
                @slot('title')
                    <i class="fas fa-chart-pie mr-1"></i> ملخص التقارير
                @endslot

                <div class="row text-center mb-3">
                    <div class="col-12 mb-3">
                        @php
                            $percent = $project->total_candidates > 0 ? round(($project->received_count / $project->total_candidates) * 100) : 0;
                            $color = $percent > 70 ? 'success' : ($percent > 40 ? 'warning' : 'danger');
                        @endphp
                        <div class="knob-label font-weight-bold mb-1">نسبة الإنجاز</div>
                        <h3 class="text-{{ $color }} font-weight-bold">{{ $percent }}%</h3>
                        <div class="progress progress-xxs mx-4">
                            <div class="progress-bar bg-{{ $color }}" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                    <div class="col-4 border-right px-1">
                        <small class="text-muted d-block small">مرشح</small>
                        <strong>{{ number_format($project->total_candidates) }}</strong>
                    </div>
                    <div class="col-4 border-right px-1">
                        <small class="text-muted d-block text-success small">مستلم</small>
                        <strong class="text-success">{{ number_format($project->received_count) }}</strong>
                    </div>
                    <div class="col-4 px-1">
                        <small class="text-muted d-block text-danger small">متبقي</small>
                        <strong class="text-danger">{{ number_format($project->not_received_count) }}</strong>
                    </div>
                </div>

                <hr>

                <h6 class="font-weight-bold small mb-2"><i class="fas fa-map-marker-alt mr-1"></i> التوزيع (المناطق):</h6>
                <div class="list-group list-group-unbordered small">
                    @forelse($areas as $areaId => $area)
                        <li class="list-group-item d-flex justify-content-between align-items-center py-1 border-0">
                            <span>{{ $area->name }}</span>
                            <span class="badge badge-light border">{{ number_format($project->area_breakdown[$areaId] ?? 0) }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted border-0 small">لا يوجد عمليات استلام</li>
                    @endforelse
                </div>

                <div class="mt-3">
                    <a href="{{ route('dashboard.reports.projects.show', $project) }}" class="btn btn-sm btn-block btn-outline-primary">
                        <i class="fas fa-external-link-alt mr-1"></i> {{ trans('projects.full_report') }}
                    </a>
                </div>
            @endcomponent
        </div>
    </div>

    {{-- قسم المستفيدين --}}
    <div class="row">
        <div class="col-md-12">
            @component('dashboard::components.box')
                @slot('title', 'المستفيدين من المشروع')

                <div class="text-center py-3">
                    <p class="mb-3">
                        <i class="fas fa-users fa-2x text-primary"></i>
                    </p>
                    <h5 class="mb-3">عدد المستفيدين: <span class="badge badge-primary badge-lg">{{ $project->beneficiaries()->count() }}</span></h5>
                    <div>
                        <a href="{{ route('dashboard.projects.beneficiaries', $project) }}" class="btn btn-primary">
                            <i class="fas fa-list"></i> عرض المستفيدين
                        </a>
                        <a href="{{ route('dashboard.projects.beneficiaries.import', $project) }}" class="btn btn-success">
                            <i class="fas fa-file-import"></i> استيراد مستفيدين
                        </a>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>

    <div class="row">
        {{-- الجهات المانحة --}}
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('title', 'الجهات المانحة')

                @if($project->grantingEntities && $project->grantingEntities->count() > 0)
                    <ul class="list-unstyled mb-0">
                        @foreach($project->grantingEntities as $partner)
                            <li class="mb-2">
                                <i class="fas fa-hands-helping text-success"></i>
                                <a href="{{ route('dashboard.suppliers.show', $partner) }}" class="text-decoration-none">
                                    {{ $partner->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle"></i> لا توجد جهات مانحة مرتبطة
                    </div>
                @endif
            @endcomponent
        </div>

        {{-- الجهات المنفذة --}}
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('title', 'الجهات المنفذة')

                @if($project->executingEntities && $project->executingEntities->count() > 0)
                    <ul class="list-unstyled mb-0">
                        @foreach($project->executingEntities as $partner)
                            <li class="mb-2">
                                <i class="fas fa-tools text-primary"></i>
                                <a href="{{ route('dashboard.suppliers.show', $partner) }}" class="text-decoration-none">
                                    {{ $partner->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle"></i> لا توجد جهات منفذة مرتبطة
                    </div>
                @endif
            @endcomponent
        </div>
    </div>

    <div class="row">
        {{-- أنواع الكوبونات --}}
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('title', 'أنواع الكوبونات والكميات')

                @if($project->couponTypes && $project->couponTypes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th><i class="fas fa-tag"></i> النوع</th>
                                    <th class="text-center" style="width: 120px;"><i class="fas fa-sort-numeric-up"></i> الكمية</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($project->couponTypes as $type)
                                    <tr>
                                        <td><strong>{{ $type->name }}</strong></td>
                                        <td class="text-center">
                                            <span class="badge badge-primary">{{ number_format($type->pivot->quantity) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <th>الإجمالي</th>
                                    <th class="text-center">
                                        <strong class="text-success">{{ number_format($project->couponTypes->sum('pivot.quantity')) }}</strong>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle"></i> لا توجد أنواع كوبونات مرتبطة
                    </div>
                @endif
            @endcomponent
        </div>

        {{-- الطرود --}}
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('title', 'الطرود المرتبطة')

                @if(($project->readyPackages && $project->readyPackages->count() > 0) || ($project->internalPackages && $project->internalPackages->count() > 0))
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th><i class="fas fa-box"></i> اسم الطرد</th>
                                    <th style="width: 140px;">النوع</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($project->readyPackages ?? [] as $pkg)
                                    <tr>
                                        <td>
                                            <a href="{{ route('dashboard.ready_packages.show', $pkg) }}" class="text-decoration-none">
                                                <strong>{{ $pkg->name }}</strong>
                                            </a>
                                        </td>
                                        <td><span class="badge badge-success"><i class="fas fa-box-open"></i> طرد جاهز</span></td>
                                    </tr>
                                @endforeach

                                @foreach($project->internalPackages ?? [] as $pkg)
                                    <tr>
                                        <td>
                                            <a href="{{ route('dashboard.internal_packages.show', $pkg) }}" class="text-decoration-none">
                                                <strong>{{ $pkg->name }}</strong>
                                            </a>
                                        </td>
                                        <td><span class="badge badge-info"><i class="fas fa-box"></i> طرد داخلي</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle"></i> لا توجد طرود مرتبطة بهذا المشروع
                    </div>
                @endif
            @endcomponent
        </div>
    </div>
</x-layout>
