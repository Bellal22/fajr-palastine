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

                    @php
                        $personWives = is_null($person->relative_id)
                            ? $person->familyMembers->filter(fn($m) => in_array($m->relationship, ['زوجة', 'wife', 'الزوجة', 'spouse']))
                            : collect();
                        $isPersonWife = !is_null($person->relative_id) && in_array($person->relationship, ['زوجة', 'wife', 'الزوجة', 'spouse']);
                    @endphp

                    @if($personWives->isNotEmpty())
                        <button type="button"
                                class="btn btn-warning btn-sm btn-block mb-1 text-dark font-weight-bold"
                                data-toggle="modal"
                                data-target="#swapRoleModal">
                            <i class="fas fa-exchange-alt"></i> تبديل الدور للزوجة (وفاة الزوج)
                        </button>
                    @elseif($isPersonWife)
                        <form action="{{ route('dashboard.people.swapRole', $person) }}"
                              method="POST"
                              class="mb-1"
                              onsubmit="return confirm('هل أنت متأكد من جعل هذه الزوجة هي ربة الأسرة (في حالة وفاة الزوج)؟\nسيتم تحويل حالتها إلى أرمل/ة ونقل جميع أفراد الأسرة والبيانات إليها وحذف سجل الزوج السابق.')">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm btn-block text-dark font-weight-bold">
                                <i class="fas fa-exchange-alt"></i> تعيين كربة أسرة (تبديل الدور)
                            </button>
                        </form>
                    @endif

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
                                    <div class="d-inline-flex align-items-center" style="gap: 4px;">
                                        <a href="{{ route('dashboard.people.show', $member) }}" class="btn btn-sm btn-outline-primary" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(in_array($member->relationship, ['زوجة', 'wife', 'الزوجة', 'spouse']))
                                            <form action="{{ route('dashboard.people.swapRole', $person) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('هل أنت متأكد من تبديل الدور وجعل الزوجة ({{ $member->first_name }} {{ $member->father_name }} {{ $member->family_name }}) هي ربة الأسرة؟\n\nسيتم:\n- تحويل الزوجة إلى ربة الأسرة بحالة أرمل/ة\n- نقل جميع الأبناء وأفراد الأسرة تحت رقم هويتها\n- نقل بيانات السكن والمنطقة والكوبونات والشكاوى إليها\n- حذف سجل الزوج الحالي نهائياً')">
                                                @csrf
                                                <input type="hidden" name="wife_id" value="{{ $member->id }}">
                                                <button type="submit" class="btn btn-sm btn-warning text-dark font-weight-bold" title="تبديل الدور: جعلها ربة الأسرة (وفاة الزوج)">
                                                    <i class="fas fa-exchange-alt"></i> تبديل الدور
                                                </button>
                                            </form>
                                        @endif
                                    </div>
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

        @if(isset($personWives) && $personWives->isNotEmpty())
            <div class="modal fade" id="swapRoleModal" tabindex="-1" role="dialog" aria-labelledby="swapRoleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="{{ route('dashboard.people.swapRole', $person) }}" method="POST">
                            @csrf
                            <div class="modal-header bg-warning text-dark">
                                <h5 class="modal-title font-weight-bold" id="swapRoleModalLabel">
                                    <i class="fas fa-exchange-alt ml-1"></i> تبديل الأدوار (وفاة الزوج / نقل رئاسة الأسرة للزوجة)
                                </h5>
                                <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-right">
                                <div class="alert alert-light border mb-3">
                                    <strong>رب الأسرة الحالي (سيتم حذفه):</strong><br>
                                    {{ $person->first_name }} {{ $person->father_name }} {{ $person->grandfather_name }} {{ $person->family_name }}
                                    <span class="badge badge-secondary mr-1">{{ $person->id_num }}</span>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">اختر الزوجة التي ستصبح ربة الأسرة الجديدة:</label>
                                    <select name="wife_id" class="form-control" required>
                                        @foreach($personWives as $wifeItem)
                                            <option value="{{ $wifeItem->id }}">
                                                {{ $wifeItem->first_name }} {{ $wifeItem->father_name }} {{ $wifeItem->grandfather_name }} {{ $wifeItem->family_name }} ({{ $wifeItem->id_num }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="alert alert-warning mb-0 small">
                                    <strong><i class="fas fa-info-circle"></i> ماذا سيحدث عند التأكيد؟</strong>
                                    <ul class="mb-0 pr-3 mt-1">
                                        <li>تصبح الزوجة المختارة هي <strong>ربة الأسرة</strong> وتتحول حالتها الاجتماعية إلى <strong>أرمل/ة</strong>.</li>
                                        <li>يتم ربط جميع الأبناء وأفراد الأسرة الآخرين برقم هوية الزوجة تلقائياً.</li>
                                        <li>تنتقل بيانات السكن، مسؤول المنطقة، المندوب، كلمة المرور، وسجل الكوبونات والشكاوى إلى الزوجة.</li>
                                        <li>يتم حذف سجل الزوج القديم نهائياً من قاعدة البيانات.</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                                <button type="submit" class="btn btn-warning text-dark font-weight-bold">
                                    <i class="fas fa-check ml-1"></i> تأكيد تبديل الدور
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
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

    {{-- Coupons --}}
    @php
        $warehousesMap = \App\Models\SubWarehouse::pluck('name', 'id')->toArray();

        $rawCoupons = $person->projects
            ->map(function ($project) use ($warehousesMap) {
                $beneficiary = $project->pivot;
                $notes = $beneficiary->notes ?? '';

                // Extract group code if tagged [مجموعة:GRP-XXXX]
                $groupCode = null;
                $customGroupName = null;
                if (preg_match('/\[مجموعة:([^\]]+)\]/u', $notes, $matches)) {
                    $parts = explode('|', trim($matches[1]), 2);
                    $groupCode = trim($parts[0]);
                    if (isset($parts[1]) && trim($parts[1]) !== '') {
                        $customGroupName = trim($parts[1]);
                    }
                }
                if (!$customGroupName && preg_match('/\[اسم_المجموعة:([^\]]+)\]/u', $notes, $matchesName)) {
                    $customGroupName = trim($matchesName[1]);
                }

                $whId = $beneficiary->sub_warehouse_id ?? null;
                $whName = $whId && isset($warehousesMap[$whId]) ? $warehousesMap[$whId] : null;

                return [
                    'project_name' => $project->name,
                    'project_id' => $project->id,
                    'total_quantity' => (int)($beneficiary->quantity ?? 0),
                    'status' => $beneficiary->status ?? 'غير مستلم',
                    'delivery_date' => $beneficiary->delivery_date ?? null,
                    'notes' => $notes,
                    'warehouse_name' => $whName,
                    'group_code' => $groupCode,
                    'custom_group_name' => $customGroupName,
                    'coupon_types' => $project->couponTypes->map(function($type) {
                        return [
                            'id' => $type->id,
                            'name' => $type->name,
                            'quantity' => $type->pivot->quantity ?? 1,
                        ];
                    }),
                ];
            })
            ->filter(fn($p) => $p['total_quantity'] > 0);

        // Grouping: Combine items that share the same group_code
        $personCoupons = collect();
        $grouped = $rawCoupons->groupBy(function($item) {
            return $item['group_code'] ?: ('single_' . $item['project_id']);
        });

        foreach ($grouped as $key => $items) {
            if (str_starts_with($key, 'single_') || $items->count() === 1) {
                $first = $items->first();
                $first['is_group'] = false;
                $personCoupons->push($first);
            } else {
                // Group of multiple parcels/coupons delivered together
                $first = $items->first();
                $allDelivered = $items->every(fn($i) => $i['status'] === 'مستلم');

                // Check custom group name from any item in the group
                $groupCustomName = null;
                foreach ($items as $item) {
                    if (!empty($item['custom_group_name'])) {
                        $groupCustomName = $item['custom_group_name'];
                        break;
                    }
                }

                $displayName = $groupCustomName ?: ('تسليم مجموعة طرود (' . $items->count() . ' طرود)');

                $personCoupons->push([
                    'is_group' => true,
                    'group_code' => $key,
                    'custom_group_name' => $groupCustomName,
                    'group_id' => preg_replace('/[^A-Za-z0-9_-]/', '_', $key),
                    'project_name' => $displayName,
                    'total_quantity' => $items->sum('total_quantity'),
                    'status' => $allDelivered ? 'مستلم' : 'مستلم جزئياً',
                    'delivery_date' => $first['delivery_date'] ?? null,
                    'warehouse_name' => $first['warehouse_name'] ?? null,
                    'group_coupons' => $items,
                ]);
            }
        }
    @endphp

    @if($personCoupons->count() > 0)
        <div class="card shadow-sm mt-3">
            <div class="card-header bg-gradient-gray text-white py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-ticket-alt"></i> الكوبونات المستلمة
                    </h6>
                    <span class="badge badge-light">{{ $personCoupons->count() }}</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th width="35%"><small>اسم الكوبون / الطرد</small></th>
                            <th width="15%" class="text-center"><small>الكمية</small></th>
                            <th width="15%" class="text-center"><small>الحالة</small></th>
                            <th width="20%"><small>تاريخ التسليم</small></th>
                            <th width="15%" class="text-center"><small>الإجراءات</small></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($personCoupons as $coupon)
                            <tr>
                                <td>
                                    @if(!empty($coupon['is_group']))
                                        <span class="badge badge-info p-1 mr-1">
                                            <i class="fas fa-boxes"></i> تسليم مجموعة
                                        </span>
                                        <strong class="group-name-title-{{ $coupon['group_code'] }}">{{ $coupon['project_name'] }}</strong>
                                        <button type="button" 
                                                class="btn btn-xs btn-outline-primary ml-1 btn-rename-group" 
                                                data-group-code="{{ $coupon['group_code'] }}" 
                                                data-current-name="{{ $coupon['custom_group_name'] ?? $coupon['project_name'] }}" 
                                                title="تعديل اسم هذه المجموعة">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <div class="small text-muted mt-1">
                                            <i class="fas fa-layer-group text-info mr-1"></i>
                                            تشمل: {{ $coupon['group_coupons']->pluck('project_name')->implode(' + ') }}
                                        </div>
                                    @else
                                        <strong>{{ $coupon['project_name'] }}</strong>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-primary badge-pill">{{ $coupon['total_quantity'] }}</span>
                                </td>
                                <td class="text-center">
                                    @if($coupon['status'] == 'مستلم')
                                        <span class="badge badge-success badge-pill">
                                            <i class="fas fa-check-circle"></i> مستلم
                                        </span>
                                    @else
                                        <span class="badge badge-secondary badge-pill">
                                            <i class="fas fa-clock"></i> {{ $coupon['status'] }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($coupon['delivery_date'])
                                        <small>
                                            <i class="fas fa-calendar-alt text-info"></i>
                                            {{ \Carbon\Carbon::parse($coupon['delivery_date'])->format('Y-m-d') }}
                                        </small>
                                    @else
                                        <small class="text-muted">-</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(!empty($coupon['is_group']))
                                        <button type="button"
                                                class="btn btn-sm btn-info"
                                                data-toggle="modal"
                                                data-target="#couponGroupModal{{ $coupon['group_id'] }}">
                                            <i class="fas fa-eye"></i> عرض التفاصيل
                                        </button>
                                    @else
                                        <button type="button"
                                                class="btn btn-sm btn-outline-info"
                                                data-toggle="modal"
                                                data-target="#couponModal{{ $coupon['project_id'] }}">
                                            <i class="fas fa-eye"></i> عرض التفاصيل
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modals for Coupon Details --}}
        @foreach($personCoupons as $coupon)
            @if(!empty($coupon['is_group']))
                {{-- Group Delivery Modal --}}
                <div class="modal fade" id="couponGroupModal{{ $coupon['group_id'] }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-info text-white">
                                <h5 class="modal-title font-weight-bold">
                                    <i class="fas fa-boxes mr-1"></i> تفاصيل تسليم مجموعة الطرود: <span class="group-name-title-{{ $coupon['group_code'] }}">{{ $coupon['project_name'] }}</span>
                                </h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                {{-- Summary Cards --}}
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="card bg-light border-0 shadow-sm">
                                            <div class="card-body py-2">
                                                <small class="text-muted d-block">إجمالي الطرود المسلمة</small>
                                                <h5 class="mb-0 text-primary font-weight-bold">{{ $coupon['total_quantity'] }} طرد</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-light border-0 shadow-sm">
                                            <div class="card-body py-2">
                                                <small class="text-muted d-block">حالة التسليم</small>
                                                <h6 class="mb-0">
                                                    <span class="badge badge-success p-1">
                                                        <i class="fas fa-check-circle"></i> تم الاستلام معاً
                                                    </span>
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-light border-0 shadow-sm">
                                            <div class="card-body py-2">
                                                <small class="text-muted d-block">تاريخ التسليم</small>
                                                <h6 class="mb-0 font-weight-bold">
                                                    @if($coupon['delivery_date'])
                                                        <i class="fas fa-calendar-alt text-info mr-1"></i>
                                                        {{ \Carbon\Carbon::parse($coupon['delivery_date'])->format('Y-m-d') }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="border-bottom pb-2 mb-3 font-weight-bold text-dark">
                                    <i class="fas fa-list-check text-info mr-1"></i>
                                    الكوبونات والطرود التي استلمها المستفيد ضمن هذه المجموعة:
                                </h6>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="5%" class="text-center">#</th>
                                                <th width="35%">اسم الكوبون / الطرد</th>
                                                <th width="15%" class="text-center">الكمية المسلمة</th>
                                                <th width="45%">محتويات وأنواع الكوبون</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($coupon['group_coupons'] as $subIdx => $subCoupon)
                                                <tr>
                                                    <td class="text-center align-middle font-weight-bold">{{ $subIdx + 1 }}</td>
                                                    <td class="align-middle">
                                                        <strong class="text-dark">{{ $subCoupon['project_name'] }}</strong>
                                                        @if(!empty($subCoupon['warehouse_name']))
                                                            <div class="small text-muted mt-1">
                                                                <i class="fas fa-warehouse text-secondary mr-1"></i> المخزن: {{ $subCoupon['warehouse_name'] }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <span class="badge badge-primary badge-pill font-weight-bold p-2">
                                                            {{ $subCoupon['total_quantity'] }}
                                                        </span>
                                                    </td>
                                                    <td class="align-middle">
                                                        @if($subCoupon['coupon_types']->count() > 0)
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-borderless mb-0">
                                                                    @foreach($subCoupon['coupon_types'] as $cType)
                                                                        <tr>
                                                                            <td class="py-1 px-1">
                                                                                <i class="fas fa-check-circle text-success mr-1"></i>
                                                                                <strong>{{ $cType['name'] }}</strong>
                                                                            </td>
                                                                            <td class="py-1 px-1 text-center" width="30%">
                                                                                <span class="badge badge-secondary badge-pill">
                                                                                    الكمية: {{ $cType['quantity'] }}
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </table>
                                                            </div>
                                                        @else
                                                            <span class="text-muted small">- طرد أساسي بدون أصناف فرعية -</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-light font-weight-bold">
                                            <tr>
                                                <td colspan="2" class="text-right">
                                                    <i class="fas fa-calculator text-success mr-1"></i> إجمالي كمية الطرود في المجموعة:
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-success badge-pill p-2">
                                                        {{ $coupon['total_quantity'] }}
                                                    </span>
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                                    <i class="fas fa-times"></i> إغلاق
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- Single Coupon Modal (Existing) --}}
                <div class="modal fade" id="couponModal{{ $coupon['project_id'] }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-gray text-white">
                                <h5 class="modal-title">
                                    <i class="fas fa-ticket-alt"></i> تفاصيل الكوبون: {{ $coupon['project_name'] }}
                                </h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                {{-- Coupon Info --}}
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="card bg-light">
                                            <div class="card-body py-2">
                                                <small class="text-muted">الكمية الإجمالية</small>
                                                <h5 class="mb-0 text-primary">{{ $coupon['total_quantity'] }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-light">
                                            <div class="card-body py-2">
                                                <small class="text-muted">الحالة</small>
                                                <h6 class="mb-0">
                                                    @if($coupon['status'] == 'مستلم')
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-check-circle"></i> مستلم
                                                        </span>
                                                    @else
                                                        <span class="badge badge-secondary">
                                                            <i class="fas fa-clock"></i> غير مستلم
                                                        </span>
                                                    @endif
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-light">
                                            <div class="card-body py-2">
                                                <small class="text-muted">تاريخ التسليم</small>
                                                <h6 class="mb-0">
                                                    @if($coupon['delivery_date'])
                                                        {{ \Carbon\Carbon::parse($coupon['delivery_date'])->format('Y-m-d') }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Coupon Types Table --}}
                                @if($coupon['coupon_types']->count() > 0)
                                    <h6 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-boxes text-info"></i> أنواع الكوبونات والكميات
                                    </h6>

                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover table-bordered mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th width="10%" class="text-center">#</th>
                                                    <th width="60%">النوع</th>
                                                    <th width="30%" class="text-center">الكمية</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($coupon['coupon_types'] as $index => $couponType)
                                                    <tr>
                                                        <td class="text-center">{{ $index + 1 }}</td>
                                                        <td>
                                                            <i class="fas fa-ticket-alt text-info"></i>
                                                            <strong>{{ $couponType['name'] }}</strong>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge badge-primary badge-pill">
                                                                {{ $couponType['quantity'] }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="bg-light">
                                                <tr>
                                                    <td colspan="2" class="text-right font-weight-bold">
                                                        <i class="fas fa-calculator text-success"></i> الإجمالي:
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-success badge-pill">
                                                            {{ $coupon['coupon_types']->sum('quantity') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-info-circle"></i>
                                        لا توجد أنواع كوبونات مرتبطة بهذا المشروع
                                    </div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                                    <i class="fas fa-times"></i> إغلاق
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endif

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Inline rename for group delivery
            $(document).on('click', '.btn-rename-group', function(e) {
                e.preventDefault();
                var groupCode = $(this).data('group-code');
                var currentName = $(this).data('current-name') || '';

                var newName = prompt('أدخل الاسم المناسب لعملية التسليم لهذه المجموعة:', currentName);
                if (newName === null) return;
                newName = newName.trim();
                if (!newName) {
                    alert('يرجى كتابة اسم مناسب للمجموعة');
                    return;
                }

                var $btn = $(this);
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

                $.ajax({
                    url: "{{ route('dashboard.projects.deliveries.rename-group') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        group_code: groupCode,
                        group_name: newName
                    },
                    success: function(res) {
                        $btn.prop('disabled', false).html('<i class="fas fa-edit"></i>');
                        if (res.success) {
                            $('.group-name-title-' + groupCode).text(newName);
                            $btn.data('current-name', newName);
                            alert('✅ ' + res.message);
                        } else {
                            alert('حدث خطأ: ' + res.message);
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html('<i class="fas fa-edit"></i>');
                        var msg = 'حدث خطأ أثناء تعديل اسم المجموعة.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        alert('❌ ' + msg);
                    }
                });
            });
        });
    </script>
    @endpush
</x-layout>
