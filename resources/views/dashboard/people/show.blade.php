<x-layout :title="$person->name" :breadcrumbs="['dashboard.people.show', $person]">

    {{-- Profile Header --}}
    <div class="row mb-4">
        <div class="col-12">
            @component('dashboard::components.box')
                @slot('class', 'bg-primary text-white')
                @slot('bodyClass', 'p-4')

                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="rounded-circle bg-white d-flex align-items-center justify-content-center"
                             style="width: 80px; height: 80px;">
                            <h2 class="text-primary mb-0">
                                {{ mb_substr($person->first_name, 0, 1) }}{{ mb_substr($person->family_name, 0, 1) }}
                            </h2>
                        </div>
                    </div>
                    <div class="col">
                        <h2 class="mb-3">
                            {{ $person->first_name }} {{ $person->father_name }}
                            {{ $person->grandfather_name }} {{ $person->family_name }}
                        </h2>
                        <div class="row">
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                                <small class="d-block" style="opacity: 0.8;">
                                    <i class="fas fa-id-card"></i> رقم الهوية
                                </small>
                                <strong>{{ $person->id_num }}</strong>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                                <small class="d-block" style="opacity: 0.8;">
                                    <i class="fas fa-phone"></i> رقم الجوال
                                </small>
                                <strong>
                                    {{ $person->phone ?? '-' }}
                                </strong>
                            </div>
                            @if($person->dob)
                                <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                                    <small class="d-block" style="opacity: 0.8;">
                                        <i class="fas fa-birthday-cake"></i> تاريخ الميلاد
                                    </small>
                                    <strong>{{ \Carbon\Carbon::parse($person->dob)->format('Y-m-d') }}</strong>
                                    <small style="opacity: 0.8;">({{ \Carbon\Carbon::parse($person->dob)->age }} سنة)</small>
                                </div>
                            @endif
                            @if($person->gender)
                                <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                                    <small class="d-block" style="opacity: 0.8;">
                                        <i class="fas fa-venus-mars"></i> الجنس
                                    </small>
                                    <strong>{{ $person->gender }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row mb-4">
        <div class="col-md-4 col-sm-6 mb-3">
            @component('dashboard::components.box')
                @slot('bodyClass', 'text-center p-4')
                <i class="fas fa-users fa-3x text-primary mb-3"></i>
                <h3 class="mb-1">{{ $person->relatives_count ?? 0 }}</h3>
                <p class="text-muted mb-0">عدد الأقارب</p>
            @endcomponent
        </div>

        <div class="col-md-4 col-sm-6 mb-3">
            @component('dashboard::components.box')
                @slot('bodyClass', 'text-center p-4')
                @if($person->has_condition == 1)
                    <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                    <h5 class="mb-1">يعاني من حالة</h5>
                @else
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5 class="mb-1">سليم</h5>
                @endif
                <p class="text-muted mb-0">الحالة الصحية</p>
            @endcomponent
        </div>

        <div class="col-md-4 col-sm-6 mb-3">
            @component('dashboard::components.box')
                @slot('bodyClass', 'text-center p-4')
                <i class="fas fa-clipboard-list fa-3x text-info mb-3"></i>
                <h3 class="mb-1">{{ \App\Models\Complaint::where('id_num', $person->id_num)->count() }}</h3>
                <p class="text-muted mb-0">الشكاوى</p>
            @endcomponent
        </div>
    </div>

    {{-- Main Content --}}
    <div class="row">
        {{-- Right Column --}}
        <div class="col-lg-8">

            {{-- المعلومات الشخصية --}}
            @component('dashboard::components.box')
                @slot('title')
                    <i class="fas fa-user-circle"></i> المعلومات الشخصية
                @endslot
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle mb-0">
                    <tbody>
                        <tr>
                            <th width="250"><i class="fas fa-user text-muted"></i> الاسم الأول</th>
                            <td>{{ $person->first_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th width="250"><i class="fas fa-user text-muted"></i> اسم الأب</th>
                            <td>{{ $person->father_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th width="250"><i class="fas fa-user text-muted"></i> اسم الجد</th>
                            <td>{{ $person->grandfather_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th width="250"><i class="fas fa-user text-muted"></i> العائلة</th>
                            <td>{{ $person->family_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th width="250"><i class="fas fa-heart text-muted"></i> الحالة الاجتماعية</th>
                            <td>
                                <span class="badge badge-secondary">
                                    {{ __($person->social_status) ?? '-' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th width="250"><i class="fas fa-key text-muted"></i> رمز المرور</th>
                            <td>{{ $person->passkey ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            @endcomponent

            {{-- معلومات السكن --}}
            @component('dashboard::components.box')
                @slot('title')
                    <i class="fas fa-home"></i> معلومات السكن والإقامة
                @endslot
                @slot('class', 'p-0 mt-4')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle mb-0">
                    <tbody>
                        <tr>
                            <th width="250"><i class="fas fa-city text-muted"></i> المدينة الأصلية</th>
                            <td>{{ __($person->city) ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th width="250"><i class="fas fa-map-marked-alt text-muted"></i> المدينة الحالية</th>
                            <td>{{ __($person->current_city) ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th width="250"><i class="fas fa-map-marker-alt text-muted"></i> الحي</th>
                            <td>{{ __($person->neighborhood) ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th width="250"><i class="fas fa-map-pin text-muted"></i> معلم بارز</th>
                            <td>{{ $person->landmark ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th width="250"><i class="fas fa-home text-muted"></i> نوع السكن</th>
                            <td>{{ __($person->housing_type) ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th width="250"><i class="fas fa-tools text-muted"></i> حالة السكن</th>
                            <td>{{ __($person->housing_damage_status) ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            @endcomponent

            {{-- الحالة الصحية --}}
            @if($person->has_condition == 1 && $person->condition_description)
                @component('dashboard::components.box')
                    @slot('title')
                        <i class="fas fa-notes-medical"></i> الحالة الصحية
                    @endslot
                    @slot('class', 'mt-4')

                    <div class="alert alert-danger mb-0">
                        <h6 class="font-weight-bold mb-2">
                            <i class="fas fa-exclamation-circle"></i> يعاني من حالة صحية
                        </h6>
                        <p class="mb-0">{{ $person->condition_description }}</p>
                    </div>
                @endcomponent
            @endif

        </div>

        {{-- Left Sidebar --}}
        <div class="col-lg-4">

            {{-- المسؤول والمندوب --}}
            @component('dashboard::components.box')
                @slot('title')
                    <i class="fas fa-user-tie"></i> الإدارة
                @endslot

                <div class="mb-3">
                    <label class="text-muted small d-block mb-2">
                        <i class="fas fa-user-shield"></i> مسؤول المنطقة
                    </label>
                    @if($person->areaResponsible)
                        <div class="p-2 bg-light rounded">
                            <strong>{{ $person->areaResponsible->name }}</strong>
                        </div>
                    @else
                        <p class="text-muted mb-0">لم يتم تحديده</p>
                    @endif
                </div>

                <div>
                    <label class="text-muted small d-block mb-2">
                        <i class="fas fa-users"></i> المندوب/الكتلة
                    </label>
                    @if($person->block)
                        <div class="p-2 bg-light rounded">
                            <strong>{{ $person->block->name }}</strong>
                        </div>
                    @else
                        <p class="text-muted mb-0">لم يتم تحديده</p>
                    @endif
                </div>
            @endcomponent

            {{-- Quick Actions --}}
            @if (auth()->user()?->isAdmin())
                @component('dashboard::components.box')
                    @slot('title')
                        <i class="fas fa-bolt"></i> إجراءات سريعة
                    @endslot

                    @if($person->areaResponsible)
                        <form action="{{ route('dashboard.people.areaResponsible.delete', $person) }}"
                              method="POST"
                              class="mb-2">
                            @csrf
                            @method('PUT')
                            <button type="submit"
                                    class="btn btn-secondary btn-block"
                                    onclick="return confirm('هل أنت متأكد من إزالة المسؤول؟')">
                                <i class="fas fa-user-times mr-2"></i> إزالة المسؤول
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('dashboard.people.destroy', $person) }}"
                          method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="btn btn-danger btn-block"
                                onclick="return confirm('هل أنت متأكد من الحذف؟')">
                            <i class="fas fa-trash mr-2"></i> حذف السجل
                        </button>
                    </form>
                @endcomponent
            @endif
        </div>
    </div>

    {{-- أفراد الأسرة --}}
    @php
        $familyMembers = \App\Models\Person::where('relative_id', $person->id_num)->paginate();
    @endphp

    @if($familyMembers->total() > 0)
        <div class="row mt-4">
            <div class="col-12">
                @component('dashboard::components.table-box')
                    @slot('title')
                        <i class="fas fa-users"></i> أفراد الأسرة
                        <span class="badge badge-primary badge-pill">{{ $familyMembers->total() }}</span>
                    @endslot

                    <thead>
                        <tr class="bg-light">
                            <th><i class="fas fa-id-card"></i> رقم الهوية</th>
                            <th><i class="fas fa-user"></i> الاسم الكامل</th>
                            <th><i class="fas fa-calendar"></i> تاريخ الميلاد</th>
                            <th><i class="fas fa-link"></i> صلة القرابة</th>
                            <th class="text-center"><i class="fas fa-heartbeat"></i> الحالة الصحية</th>
                            <th class="text-center"><i class="fas fa-cog"></i> الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($person->familyMembers as $member)
                            <tr>
                                <td>
                                    <a href="{{ route('dashboard.people.show', $member) }}"
                                       class="text-primary font-weight-bold">
                                        {{ $member->id_num }}
                                    </a>
                                </td>
                                <td>
                                    {{ $member->first_name }} {{ $member->father_name }}
                                    {{ $member->grandfather_name }} {{ $member->family_name }}
                                </td>
                                <td>{{ $member->dob ?? '-' }}</td>
                                <td>
                                    <span class="badge badge-secondary">
                                        {{ __($member->relationship) ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($member->has_condition == 1)
                                        <span class="badge badge-danger">
                                            <i class="fas fa-exclamation-circle"></i> يعاني
                                        </span>
                                    @elseif($member->has_condition == 0)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i> سليم
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('dashboard.people.show', $member) }}"
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> عرض
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    @if($familyMembers->hasPages())
                        @slot('footer')
                            {{ $familyMembers->links() }}
                        @endslot
                    @endif
                @endcomponent
            </div>
        </div>
    @endif

    {{-- الشكاوى --}}
    @php
        $complaints = \App\Models\Complaint::where('id_num', $person->id_num)->paginate();
    @endphp

    @if($complaints->total() > 0)
        <div class="row mt-4">
            <div class="col-12">
                @component('dashboard::components.table-box')
                    @slot('title')
                        <i class="fas fa-clipboard-list"></i> الشكاوى والملاحظات
                        <span class="badge badge-info badge-pill">{{ $complaints->total() }}</span>
                    @endslot

                    <thead>
                        <tr class="bg-light">
                            <th><i class="fas fa-hashtag"></i> رقم الشكوى</th>
                            <th><i class="fas fa-file-alt"></i> العنوان</th>
                            <th><i class="fas fa-flag"></i> الحالة</th>
                            <th><i class="fas fa-calendar"></i> التاريخ</th>
                            <th class="text-center"><i class="fas fa-cog"></i> الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($complaints as $complaint)
                            <tr>
                                <td>
                                    <a href="{{ route('dashboard.complaints.show', $complaint) }}"
                                       class="text-primary font-weight-bold">
                                        #{{ $complaint->id }}
                                    </a>
                                </td>
                                <td>
                                    <strong>{{ Str::limit($complaint->complaint_title, 50) }}</strong>
                                    @if($complaint->response)
                                        <br>
                                        <small class="text-success">
                                            <i class="fas fa-check-circle"></i> تم الرد
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'pending' => 'secondary',
                                            'in_progress' => 'info',
                                            'resolved' => 'success',
                                            'rejected' => 'danger',
                                        ];
                                        $statusLabels = [
                                            'pending' => 'قيد الانتظار',
                                            'in_progress' => 'قيد المعالجة',
                                            'resolved' => 'تم الحل',
                                            'rejected' => 'مرفوضة',
                                        ];
                                    @endphp
                                    <span class="badge badge-{{ $statusColors[$complaint->status ?? 'pending'] }}">
                                        {{ $statusLabels[$complaint->status ?? 'pending'] }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $complaint->created_at->diffForHumans() }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('dashboard.complaints.show', $complaint) }}"
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> عرض
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    @if($complaints->hasPages())
                        @slot('footer')
                            {{ $complaints->links() }}
                        @endslot
                    @endif
                @endcomponent
            </div>
        </div>
    @endif

</x-layout>
