<x-layout :title="trans('projects.plural')" :breadcrumbs="['dashboard.projects.index']">
    @include('dashboard.projects.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('projects.actions.list') ({{ $projects->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <div class="d-flex">
                <x-check-all-delete
                        type="{{ \App\Models\Project::class }}"
                        :resource="trans('projects.plural')"></x-check-all-delete>

                <div class="ml-2 d-flex justify-content-between flex-grow-1">
                    @include('dashboard.projects.partials.actions.create')
                    @include('dashboard.projects.partials.actions.trashed')
                </div>
            </div>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('projects.attributes.name')</th>
            <th>@lang('projects.attributes.description')</th>
            <th>@lang('projects.attributes.start_date')</th>
            <th>@lang('projects.attributes.end_date')</th>
            <th>@lang('projects.attributes.status')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($projects as $project)
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$project"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.projects.show', $project) }}"
                       class="text-decoration-none text-ellipsis">
                        <strong>{{ $project->name }}</strong>
                    </a>
                </td>
                <td>
                    <span class="text-muted text-ellipsis" style="max-width: 200px; display: inline-block;">
                        {{ Str::limit($project->description, 50) ?? '-' }}
                    </span>
                </td>
                <td>
                    @if($project->start_date)
                        <i class="fas fa-calendar-alt text-info"></i> {{ $project->start_date }}
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    @if($project->end_date)
                        <i class="fas fa-calendar-check text-success"></i> {{ $project->end_date }}
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    @if($project->status === 'active')
                        <span class="badge badge-success">@lang('projects.status.active')</span>
                    @elseif($project->status === 'completed')
                        <span class="badge badge-primary">@lang('projects.status.completed')</span>
                    @elseif($project->status === 'suspended')
                        <span class="badge badge-warning">@lang('projects.status.suspended')</span>
                    @endif
                </td>

                <td style="width: 160px">
                    @include('dashboard.projects.partials.actions.show')
                    @include('dashboard.projects.partials.actions.edit')
                    @include('dashboard.projects.partials.actions.delete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('projects.empty')</td>
            </tr>
        @endforelse

        @if($projects->hasPages())
            @slot('footer')
                {{ $projects->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
