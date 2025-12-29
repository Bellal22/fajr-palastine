<x-layout :title="$project->name" :breadcrumbs="['dashboard.projects.show', $project]">
    <div class="row">
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('title', 'معلومات الكوبون')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle">
                    <tbody>
                    <tr>
                        <th width="200">@lang('projects.attributes.name')</th>
                        <td>{{ $project->name }}</td>
                    </tr>
                    <tr>
                        <th>@lang('projects.attributes.status')</th>
                        <td>
                            @if($project->status == 'active')
                                <span class="badge badge-success">نشط</span>
                            @elseif($project->status == 'completed')
                                <span class="badge badge-primary">مكتمل</span>
                            @else
                                <span class="badge badge-warning">معلق</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('projects.attributes.start_date')</th>
                        <td>{{ $project->start_date ? $project->start_date->format('Y-m-d') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>@lang('projects.attributes.end_date')</th>
                        <td>{{ $project->end_date ? $project->end_date->format('Y-m-d') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>@lang('projects.attributes.description')</th>
                        <td>{{ $project->description ?? '-' }}</td>
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
        
        <div class="col-md-6">
            {{-- الشركاء --}}
            @component('dashboard::components.box')
                @slot('title', 'الشركاء')
                
                <strong>الجهات المانحة:</strong>
                <ul>
                    @forelse($project->grantingEntities as $partner)
                        <li><a href="{{ route('dashboard.suppliers.show', $partner) }}">{{ $partner->name }}</a></li>
                    @empty
                        <li class="text-muted">لا يوجد</li>
                    @endforelse
                </ul>

                <hr>

                <strong>الجهات المنفذة:</strong>
                <ul>
                    @forelse($project->executingEntities as $partner)
                        <li><a href="{{ route('dashboard.suppliers.show', $partner) }}">{{ $partner->name }}</a></li>
                    @empty
                        <li class="text-muted">لا يوجد</li>
                    @endforelse
                </ul>
            @endcomponent
        </div>
    </div>

    <div class="row">
        {{-- أنواع الكوبونات --}}
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('title', 'أنواع الكوبونات والكميات')
                
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>النوع</th>
                            <th>الكمية</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($project->couponTypes as $type)
                            <tr>
                                <td>{{ $type->name }}</td>
                                <td>{{ $type->pivot->quantity }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-center">لا يوجد بيانات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            @endcomponent
        </div>

        {{-- الطرود --}}
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('title', 'الطرود المرتبطة')
                
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>اسم الطرد</th>
                            <th>النوع</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($project->readyPackages as $pkg)
                            <tr>
                                <td>{{ $pkg->name }}</td>
                                <td><span class="badge badge-info">طرد جاهز</span></td>
                            </tr>
                        @empty
                        @endforelse
                        
                        @forelse($project->internalPackages as $pkg)
                            <tr>
                                <td>{{ $pkg->name }}</td>
                                <td><span class="badge badge-secondary">طرد داخلي</span></td>
                            </tr>
                        @empty
                        @endforelse

                        @if($project->readyPackages->isEmpty() && $project->internalPackages->isEmpty())
                            <tr><td colspan="2" class="text-center">لا يوجد طرود مختارة</td></tr>
                        @endif
                    </tbody>
                </table>
            @endcomponent
        </div>
    </div>
</x-layout>
