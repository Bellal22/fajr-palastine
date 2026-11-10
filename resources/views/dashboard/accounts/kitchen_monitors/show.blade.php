<x-layout :title="$kitchenMonitor->name" :breadcrumbs="['dashboard.kitchen_monitors.show', $kitchenMonitor]">
    @component('dashboard::components.box')
        @slot('bodyClass', 'p-4')

        <div class="row">
            {{-- Profile Header Section --}}
            <div class="col-md-4 text-center mb-4 mb-md-0">
                <div class="profile-avatar mb-3 position-relative d-inline-block">
                    @if($kitchenMonitor->getFirstMedia('avatars'))
                        <file-preview :media="{{ $kitchenMonitor->getMediaResource('avatars') }}"></file-preview>
                    @else
                        <img src="{{ $kitchenMonitor->getAvatar() }}"
                             class="rounded-circle img-thumbnail"
                             width="150"
                             height="150"
                             alt="{{ $kitchenMonitor->name }}">
                    @endif
                </div>
                <h4 class="mb-1">{{ $kitchenMonitor->name }}</h4>
                <p class="text-muted mb-2">
                    <i class="fas fa-user-tie"></i> متابع مطبخ
                </p>
                {{-- Status Badge --}}
                <div class="mb-0">
                    @if($kitchenMonitor->person_status == 'فعال')
                        <span class="badge badge-success">
                            <i class="fas fa-check-circle"></i> {{ $kitchenMonitor->person_status }}
                        </span>
                    @else
                        <span class="badge badge-danger">
                            <i class="fas fa-times-circle"></i> {{ $kitchenMonitor->person_status ?? 'غير فعال' }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Profile Details Section --}}
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle text-primary"></i>
                            تفاصيل الحساب
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <strong class="text-muted">
                                    <i class="fas fa-user"></i> الاسم
                                </strong>
                            </div>
                            <div class="col-sm-8">
                                {{ $kitchenMonitor->name }}
                            </div>
                        </div>

                        <hr class="my-2">

                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <strong class="text-muted">
                                    <i class="fas fa-envelope"></i> البريد الإلكتروني
                                </strong>
                            </div>
                            <div class="col-sm-8">
                                <a href="mailto:{{ $kitchenMonitor->email }}" class="text-decoration-none">
                                    {{ $kitchenMonitor->email }}
                                </a>
                            </div>
                        </div>

                        <hr class="my-2">

                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <strong class="text-muted">
                                    <i class="fas fa-phone"></i> الهاتف
                                </strong>
                            </div>
                            <div class="col-sm-8">
                                @if($kitchenMonitor->phone)
                                    <a href="tel:{{ $kitchenMonitor->phone }}" class="text-decoration-none">
                                        {{ $kitchenMonitor->phone }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>

                        <hr class="my-2">

                        <div class="row mb-0">
                            <div class="col-sm-4">
                                <strong class="text-muted">
                                    <i class="fas fa-toggle-on"></i> حالة الحساب
                                </strong>
                            </div>
                            <div class="col-sm-8">
                                @if($kitchenMonitor->person_status == 'فعال')
                                    <span class="text-success font-weight-bold">
                                        <i class="fas fa-check-circle"></i> {{ $kitchenMonitor->person_status }}
                                    </span>
                                @else
                                    <span class="text-danger font-weight-bold">
                                        <i class="fas fa-times-circle"></i> {{ $kitchenMonitor->person_status ?? 'غير فعال' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @slot('footer')
            <div class="d-flex gap-2 flex-wrap">
                @include('dashboard.accounts.kitchen_monitors.partials.actions.impersonate')
                @include('dashboard.accounts.kitchen_monitors.partials.actions.edit')
                @include('dashboard.accounts.kitchen_monitors.partials.actions.delete')
                @include('dashboard.accounts.kitchen_monitors.partials.actions.restore')
                @include('dashboard.accounts.kitchen_monitors.partials.actions.forceDelete')
            </div>
        @endslot
    @endcomponent
</x-layout>
