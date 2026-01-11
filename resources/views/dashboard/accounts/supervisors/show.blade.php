<x-layout :title="$supervisor->name" :breadcrumbs="['dashboard.supervisors.show', $supervisor]">
    @component('dashboard::components.box')
        @slot('bodyClass', 'p-4')

        <div class="row">
            {{-- Profile Header Section --}}
            <div class="col-md-4 text-center mb-4 mb-md-0">
                <div class="profile-avatar mb-3 position-relative d-inline-block">
                    @if($supervisor->getFirstMedia('avatars'))
                        <file-preview :media="{{ $supervisor->getMediaResource('avatars') }}"></file-preview>
                    @else
                        <img src="{{ $supervisor->getAvatar() }}"
                             class="rounded-circle img-thumbnail"
                             width="150"
                             height="150"
                             alt="{{ $supervisor->name }}">
                    @endif
                    {{-- Status Flag --}}
                    <span class="position-absolute" style="bottom: 10px; right: 10px;">
                        @include('dashboard.accounts.supervisors.partials.flags.svg')
                    </span>
                </div>
                <h4 class="mb-1">{{ $supervisor->name }}</h4>
                <p class="text-muted mb-2">
                    <i class="fas fa-user-tie"></i> @lang('supervisors.singular')
                </p>
                {{-- Status Badge --}}
                <div class="mb-0">
                    @if($supervisor->person_status == 'فعال')
                        <span class="badge badge-success">
                            <i class="fas fa-check-circle"></i> {{ $supervisor->person_status }}
                        </span>
                    @else
                        <span class="badge badge-danger">
                            <i class="fas fa-times-circle"></i> {{ $supervisor->person_status ?? 'غير فعال' }}
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
                            @lang('supervisors.profile_details')
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <strong class="text-muted">
                                    <i class="fas fa-user"></i> @lang('supervisors.attributes.name')
                                </strong>
                            </div>
                            <div class="col-sm-8">
                                {{ $supervisor->name }}
                            </div>
                        </div>

                        <hr class="my-2">

                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <strong class="text-muted">
                                    <i class="fas fa-envelope"></i> @lang('supervisors.attributes.email')
                                </strong>
                            </div>
                            <div class="col-sm-8">
                                <a href="mailto:{{ $supervisor->email }}" class="text-decoration-none">
                                    {{ $supervisor->email }}
                                </a>
                            </div>
                        </div>

                        <hr class="my-2">

                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <strong class="text-muted">
                                    <i class="fas fa-phone"></i> @lang('supervisors.attributes.phone')
                                </strong>
                            </div>
                            <div class="col-sm-8">
                                @if($supervisor->phone)
                                    <a href="tel:{{ $supervisor->phone }}" class="text-decoration-none">
                                        {{ $supervisor->phone }}
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
                                    <i class="fas fa-toggle-on"></i> @lang('supervisors.attributes.person_status')
                                </strong>
                            </div>
                            <div class="col-sm-8">
                                @if($supervisor->person_status == 'فعال')
                                    <span class="text-success font-weight-bold">
                                        <i class="fas fa-check-circle"></i> {{ $supervisor->person_status }}
                                    </span>
                                @else
                                    <span class="text-danger font-weight-bold">
                                        <i class="fas fa-times-circle"></i> {{ $supervisor->person_status ?? 'غير فعال' }}
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
                @include('dashboard.accounts.supervisors.partials.actions.impersonate')
                @include('dashboard.accounts.supervisors.partials.actions.edit')
                @include('dashboard.accounts.supervisors.partials.actions.delete')
                @include('dashboard.accounts.supervisors.partials.actions.restore')
                @include('dashboard.accounts.supervisors.partials.actions.forceDelete')
            </div>
        @endslot
    @endcomponent
</x-layout>
