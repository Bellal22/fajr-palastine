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
