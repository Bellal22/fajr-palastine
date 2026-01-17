<x-layout :title="$person->name" :breadcrumbs="['dashboard.people.index', 'dashboard.people.show']">

    {{-- Health Alert (Priority) --}}
    @if($person->has_condition == 1 && $person->condition_description)
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-3" role="alert">
            <div class="d-flex align-items-center">
                <div class="{{ app()->isLocale('ar') ? 'ml-3' : 'mr-3' }}">
                    <i class="fas fa-exclamation-triangle fa-3x"></i>
                </div>
                <div class="flex-grow-1">
                    <h5 class="alert-heading mb-1">
                        <i class="fas fa-notes-medical"></i> @lang('people.messages.health_alert')
                    </h5>
                    <p class="mb-0">{{ $person->condition_description }}</p>
                </div>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        {{-- Sidebar --}}
        <div class="col-xl-3 col-lg-4">

            {{-- Profile Card --}}
            <div class="card shadow-sm mb-3">
                <div class="card-body text-center p-3">
                    {{-- Avatar --}}
                    <div class="mb-3">
                        <div class="avatar-circle mx-auto shadow"
                             style="width: 90px; height: 90px; background-color: #667eea; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user fa-3x text-white"></i>
                        </div>
                    </div>

                    {{-- Name --}}
                    <h6 class="font-weight-bold mb-1" style="line-height: 1.3;">
                        {{ $person->first_name }} {{ $person->father_name }}<br>
                        {{ $person->grandfather_name }} {{ $person->family_name }}
                    </h6>

                    {{-- ID --}}
                    <p class="text-muted small mb-2">
                        <i class="fas fa-id-card"></i> {{ $person->id_num }}
                    </p>

                    {{-- Status Badge --}}
                    @if($person->has_condition == 1)
                        <span class="badge badge-danger badge-pill">
                            <i class="fas fa-exclamation-circle"></i> @lang('people.health_status.has_condition')
                        </span>
                    @else
                        <span class="badge badge-success badge-pill">
                            <i class="fas fa-check-circle"></i> @lang('people.health_status.healthy')
                        </span>
                    @endif
                </div>

                {{-- Quick Stats --}}
                <div class="list-group list-group-flush">
                    @if($person->phone)
                    <div class="list-group-item py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-phone text-success"></i> @lang('people.attributes.phone')
                            </small>
                            <a href="tel:{{ $person->phone }}" class="badge badge-light text-dark text-decoration-none">
                                {{ $person->phone }}
                            </a>
                        </div>
                    </div>
                    @endif

                    @if($person->dob)
                    <div class="list-group-item py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-birthday-cake text-info"></i> @lang('people.attributes.age')
                            </small>
                            <span class="badge badge-info">
                                {{ \Carbon\Carbon::parse($person->dob)->age }} @lang('people.attributes.years')
                            </span>
                        </div>
                    </div>
                    @endif

                    @if($person->gender)
                    <div class="list-group-item py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-venus-mars text-primary"></i> @lang('people.attributes.gender')
                            </small>
                            <span class="badge badge-primary">{{ $person->gender }}</span>
                        </div>
                    </div>
                    @endif

                    <div class="list-group-item py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-users text-gray"></i> @lang('people.attributes.relatives')
                            </small>
                            <span class="badge badge-gray">{{ $person->relatives_count ?? 0 }}</span>
                        </div>
                    </div>

                    <div class="list-group-item py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-clipboard-list text-info"></i> @lang('people.attributes.complaints')
                            </small>
                            <span class="badge badge-info">
                                {{ \App\Models\Complaint::where('id_num', $person->id_num)->count() }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="card-footer p-2">
                    <a href="{{ route('dashboard.people.edit', $person) }}"
                       class="btn btn-primary btn-sm btn-block mb-1">
                        <i class="fas fa-edit"></i> @lang('people.actions.edit')
                    </a>

                    @if (auth()->user()?->isAdmin())
                        @if($person->areaResponsible)
                            @php
                                $removeMsg = __('people.dialogs.remove_responsible');
                            @endphp
                            <form action="{{ route('dashboard.people.areaResponsible.delete', $person) }}"
                                  method="POST" class="mb-1"
                                  onsubmit="return confirm('{{ $removeMsg }}')">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success btn-sm btn-block">
                                    <i class="fas fa-user-times"></i> @lang('people.actions.remove_responsible')
                                </button>
                            </form>
                        @endif

                        @php
                            $deleteMsg = __('people.dialogs.delete');
                        @endphp
                        <form action="{{ route('dashboard.people.destroy', $person) }}"
                              method="POST"
                              onsubmit="return confirm('{{ $deleteMsg }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm btn-block">
                                <i class="fas fa-trash"></i> @lang('people.actions.delete')
                            </button>
                        </form>
                    @endif
                </div>
            </div>

        </div>

        {{-- Main Content --}}
        <div class="col-xl-9 col-lg-8">

            <div class="row">
                {{-- Personal Info --}}
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-primary text-white py-2">
                            <h6 class="mb-0">
                                <i class="fas fa-user"></i> @lang('people.sections.personal_info')
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped mb-0">
                                    <tbody>
                                        <tr>
                                            <td width="45%" class="text-muted">
                                                <i class="fas fa-user text-primary"></i> @lang('people.attributes.first_name')
                                            </td>
                                            <td class="font-weight-bold">{{ $person->first_name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">
                                                <i class="fas fa-user text-primary"></i> @lang('people.attributes.father_name')
                                            </td>
                                            <td class="font-weight-bold">{{ $person->father_name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">
                                                <i class="fas fa-user text-primary"></i> @lang('people.attributes.grandfather_name')
                                            </td>
                                            <td class="font-weight-bold">{{ $person->grandfather_name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">
                                                <i class="fas fa-users text-primary"></i> @lang('people.attributes.family_name')
                                            </td>
                                            <td class="font-weight-bold">{{ $person->family_name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">
                                                <i class="fas fa-birthday-cake text-info"></i> @lang('people.attributes.dob')
                                            </td>
                                            <td>
                                                @if($person->dob)
                                                    {{ \Carbon\Carbon::parse($person->dob)->format('Y-m-d') }}
                                                    <small class="text-muted">({{ \Carbon\Carbon::parse($person->dob)->age }} @lang('people.attributes.years'))</small>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">
                                                <i class="fas fa-heart text-danger"></i> @lang('people.attributes.social_status')
                                            </td>
                                            <td>
                                                @if($person->social_status)
                                                    <span class="badge badge-info">{{ __($person->social_status) }}</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">
                                                <i class="fas fa-key text-gray"></i> @lang('people.attributes.passkey')
                                            </td>
                                            <td>
                                                @if($person->passkey)
                                                    <code class="px-2 py-1">{{ $person->passkey }}</code>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Housing Info --}}
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-secondary text-white py-2">
                            <h6 class="mb-0">
                                <i class="fas fa-home"></i> @lang('people.sections.housing_info')
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped mb-0">
                                    <tbody>
                                        <tr>
                                            <td width="45%" class="text-muted">
                                                <i class="fas fa-city text-secondary"></i> @lang('people.attributes.city')
                                            </td>
                                            <td>
                                                @if($person->city)
                                                    <span class="badge badge-secondary">{{ __($person->city) }}</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">
                                                <i class="fas fa-map-marked-alt text-info"></i> @lang('people.attributes.current_city')
                                            </td>
                                            <td>
                                                @if($person->current_city)
                                                    <span class="badge badge-primary">{{ __($person->current_city) }}</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">
                                                <i class="fas fa-map-marker-alt text-danger"></i> @lang('people.attributes.neighborhood')
                                            </td>
                                            <td>{{ $person->neighborhood ? __($person->neighborhood) : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">
                                                <i class="fas fa-map-pin text-success"></i> @lang('people.attributes.landmark')
                                            </td>
                                            <td>{{ $person->landmark ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">
                                                <i class="fas fa-building text-primary"></i> @lang('people.attributes.housing_type')
                                            </td>
                                            <td>
                                                @if($person->housing_type)
                                                    <span class="badge badge-info">{{ __($person->housing_type) }}</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">
                                                <i class="fas fa-tools text-gray"></i> @lang('people.attributes.housing_damage_status')
                                            </td>
                                            <td>
                                                @if($person->housing_damage_status)
                                                    <span class="badge badge-gray">{{ __($person->housing_damage_status) }}</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Management Info --}}
                <div class="col-12 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white py-2">
                            <h6 class="mb-0">
                                <i class="fas fa-user-tie"></i> @lang('people.sections.management_info')
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <div class="media p-3 bg-light rounded">
                                        <i class="fas fa-user-tie fa-2x text-success {{ app()->isLocale('ar') ? 'ml-3' : 'mr-3' }}"></i>
                                        <div class="media-body">
                                            <h6 class="mt-0 mb-1 text-muted small">@lang('people.attributes.area_responsible')</h6>
                                            @if($person->areaResponsible)
                                                <a href="{{ route('dashboard.area_responsibles.show', $person->areaResponsible) }}"
                                                   class="font-weight-bold">
                                                    {{ $person->areaResponsible->name }}
                                                    <i class="fas fa-external-link-alt small"></i>
                                                </a>
                                            @else
                                                <span class="text-muted">@lang('people.messages.not_assigned')</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="media p-3 bg-light rounded">
                                        <i class="fas fa-walking fa-2x text-info {{ app()->isLocale('ar') ? 'ml-3' : 'mr-3' }}"></i>
                                        <div class="media-body">
                                            <h6 class="mt-0 mb-1 text-muted small">@lang('people.attributes.block')</h6>
                                            @if($person->block)
                                                <a href="{{ route('dashboard.blocks.show', $person->block) }}"
                                                   class="font-weight-bold">
                                                    {{ $person->block->name }}
                                                    <i class="fas fa-external-link-alt small"></i>
                                                </a>
                                            @else
                                                <span class="text-muted">@lang('people.messages.not_assigned')</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Family Members --}}
    @php
        $familyMembers = \App\Models\Person::where('relative_id', $person->id_num)->paginate(10);
    @endphp

    @if($familyMembers->total() > 0)
        <div class="card shadow-sm mt-3">
            <div class="card-header bg-gradient-primary text-white py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-users"></i> @lang('people.sections.family_members')
                    </h6>
                    <span class="badge badge-light">{{ $familyMembers->total() }}</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th width="12%"><small>@lang('people.attributes.id_num')</small></th>
                            <th width="32%"><small>@lang('people.attributes.full_name')</small></th>
                            <th width="8%"><small>@lang('people.attributes.age')</small></th>
                            <th width="10%"><small>@lang('people.attributes.gender')</small></th>
                            <th width="12%"><small>@lang('people.attributes.relationship')</small></th>
                            <th width="16%" class="text-center"><small>@lang('people.attributes.health_status')</small></th>
                            <th width="10%" class="text-center"><small>@lang('people.actions.actions')</small></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($person->familyMembers as $member)
                            <tr>
                                <td>
                                    <a href="{{ route('dashboard.people.show', $member) }}" class="text-primary font-weight-bold">
                                        {{ $member->id_num }}
                                    </a>
                                </td>
                                <td>
                                    {{ $member->first_name }} {{ $member->father_name }}
                                    {{ $member->grandfather_name }} {{ $member->family_name }}
                                </td>
                                <td>
                                    @if($member->dob)
                                        <span class="badge badge-light">{{ \Carbon\Carbon::parse($member->dob)->age }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($member->gender)
                                        <span class="badge badge-secondary badge-pill">{{ $member->gender }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($member->relationship)
                                        <span class="badge badge-info badge-pill">{{ __($member->relationship) }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($member->has_condition == 1)
                                        <span class="badge badge-danger badge-pill">
                                            <i class="fas fa-exclamation-circle"></i> @lang('people.health_status.has_condition_short')
                                        </span>
                                    @elseif($member->has_condition == 0)
                                        <span class="badge badge-success badge-pill">
                                            <i class="fas fa-check"></i> @lang('people.health_status.healthy_short')
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('dashboard.people.show', $member) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($familyMembers->hasPages())
                <div class="card-footer py-2">
                    {{ $familyMembers->links() }}
                </div>
            @endif
        </div>
    @endif

    {{-- Complaints --}}
    @php
        $complaints = \App\Models\Complaint::where('id_num', $person->id_num)->paginate(10);
    @endphp

    @if($complaints->total() > 0)
        <div class="card shadow-sm mt-3">
            <div class="card-header bg-gradient-info text-white py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-clipboard-list"></i> @lang('people.sections.complaints')
                    </h6>
                    <span class="badge badge-light">{{ $complaints->total() }}</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th width="8%"><small>@lang('complaints.attributes.number')</small></th>
                            <th width="44%"><small>@lang('complaints.attributes.title')</small></th>
                            <th width="15%"><small>@lang('complaints.attributes.status')</small></th>
                            <th width="18%"><small>@lang('complaints.attributes.date')</small></th>
                            <th width="15%" class="text-center"><small>@lang('complaints.actions.actions')</small></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($complaints as $complaint)
                            <tr>
                                <td>
                                    <a href="{{ route('dashboard.complaints.show', $complaint) }}" class="text-primary font-weight-bold">
                                        #{{ $complaint->id }}
                                    </a>
                                </td>
                                <td>
                                    {{ Str::limit($complaint->complaint_title, 65) }}
                                    @if($complaint->response)
                                        <br><small class="text-success">
                                            <i class="fas fa-check-circle"></i> @lang('complaints.status.replied')
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statuses = [
                                            'pending' => ['color' => 'secondary', 'icon' => 'clock', 'key' => 'pending'],
                                            'in_progress' => ['color' => 'info', 'icon' => 'spinner', 'key' => 'in_progress'],
                                            'resolved' => ['color' => 'success', 'icon' => 'check-circle', 'key' => 'resolved'],
                                            'rejected' => ['color' => 'danger', 'icon' => 'times-circle', 'key' => 'rejected'],
                                        ];
                                        $s = $statuses[$complaint->status ?? 'pending'];
                                    @endphp
                                    <span class="badge badge-{{ $s['color'] }} badge-pill">
                                        <i class="fas fa-{{ $s['icon'] }}"></i> @lang('complaints.status.' . $s['key'])
                                    </span>
                                </td>
                                <td>
                                    <small>
                                        {{ $complaint->created_at->format('Y-m-d') }}<br>
                                        <span class="text-info">{{ $complaint->created_at->diffForHumans() }}</span>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('dashboard.complaints.show', $complaint) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($complaints->hasPages())
                <div class="card-footer py-2">
                    {{ $complaints->links() }}
                </div>
            @endif
        </div>
    @endif

</x-layout>
