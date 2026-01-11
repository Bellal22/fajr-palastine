<x-layout :title="$complaint->complaint_title" :breadcrumbs="['dashboard.complaints.show', $complaint]">

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
                            <i class="fas fa-clipboard-list fa-3x text-primary"></i>
                        </div>
                    </div>
                    <div class="col">
                        <h2 class="mb-3">
                            {{ $complaint->complaint_title }}
                        </h2>
                        <div class="row">
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                                <small class="d-block" style="opacity: 0.8;">
                                    <i class="fas fa-hashtag"></i> @lang('complaints.attributes.complaint_number')
                                </small>
                                <strong>#{{ $complaint->id }}</strong>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                                <small class="d-block" style="opacity: 0.8;">
                                    <i class="fas fa-id-card"></i> @lang('complaints.attributes.id_num')
                                </small>
                                <strong>{{ $complaint->id_num }}</strong>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                                <small class="d-block" style="opacity: 0.8;">
                                    <i class="fas fa-calendar"></i> @lang('complaints.attributes.submission_date')
                                </small>
                                <strong>{{ $complaint->created_at->format('Y-m-d') }}</strong>
                                <small style="opacity: 0.8;">({{ $complaint->created_at->diffForHumans() }})</small>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                                <small class="d-block" style="opacity: 0.8;">
                                    <i class="fas fa-flag"></i> @lang('complaints.attributes.status')
                                </small>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'in_progress' => 'info',
                                        'resolved' => 'success',
                                        'rejected' => 'danger',
                                    ];
                                @endphp
                                <span class="badge badge-{{ $statusColors[$complaint->status] ?? 'secondary' }}">
                                    @lang('complaints.status.' . $complaint->status)
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>

    {{-- Main Content --}}
    <div class="row">
        {{-- Right Column --}}
        <div class="col-lg-8">

            {{-- معلومات مقدم الشكوى --}}
            @if($complaint->person)
                @component('dashboard::components.box')
                    @slot('title')
                        <i class="fas fa-user-circle"></i> @lang('complaints.sections.complainant_info')
                    @endslot
                    @slot('class', 'p-0')
                    @slot('bodyClass', 'p-0')

                    <table class="table table-striped table-middle mb-0">
                        <tbody>
                            <tr>
                                <th width="250"><i class="fas fa-id-card text-muted"></i> @lang('complaints.attributes.id_num')</th>
                                <td>
                                    <a href="{{ route('dashboard.people.show', $complaint->person) }}">
                                        {{ $complaint->id_num }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th width="250"><i class="fas fa-user text-muted"></i> @lang('complaints.attributes.full_name')</th>
                                <td>
                                    {{ $complaint->person->first_name }}
                                    {{ $complaint->person->father_name }}
                                    {{ $complaint->person->grandfather_name }}
                                    {{ $complaint->person->family_name }}
                                </td>
                            </tr>
                            <tr>
                                <th width="250"><i class="fas fa-phone text-muted"></i> @lang('complaints.attributes.phone')</th>
                                <td>{{ $complaint->person->phone ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                @endcomponent
            @endif

            {{-- نص الشكوى --}}
            @component('dashboard::components.box')
                @slot('title')
                    <i class="fas fa-file-alt"></i> @lang('complaints.sections.complaint_text')
                @endslot
                @slot('class', 'mt-4')

                <p style="white-space: pre-line; line-height: 1.8;">{{ $complaint->complaint_text }}</p>
            @endcomponent

            {{-- الرد على الشكوى --}}
            @if($complaint->response)
                @component('dashboard::components.box')
                    @slot('title')
                        <i class="fas fa-reply"></i> @lang('complaints.sections.response')
                    @endslot
                    @slot('class', 'p-0 mt-4')
                    @slot('bodyClass', 'p-0')

                    <table class="table table-striped table-middle mb-0">
                        <tbody>
                            <tr>
                                <th width="250"><i class="fas fa-comment text-muted"></i> @lang('complaints.attributes.response_text')</th>
                                <td style="white-space: pre-line;">{{ $complaint->response }}</td>
                            </tr>
                            <tr>
                                <th width="250"><i class="fas fa-calendar text-muted"></i> @lang('complaints.attributes.response_date')</th>
                                <td>{{ $complaint->responded_at?->format('Y-m-d H:i') }}</td>
                            </tr>
                            @if($complaint->responder)
                                <tr>
                                    <th width="250"><i class="fas fa-user-tie text-muted"></i> @lang('complaints.attributes.responded_by')</th>
                                    <td>{{ $complaint->responder->name }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                @endcomponent
            @endif

        </div>

        {{-- Left Sidebar --}}
        <div class="col-lg-4">

            {{-- نموذج الرد --}}
            @component('dashboard::components.box')
                @slot('title')
                    <i class="fas fa-reply"></i> @lang('complaints.sections.response_form')
                @endslot

                <form action="{{ route('dashboard.complaints.respond', $complaint) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="text-muted small d-block mb-2">
                            <i class="fas fa-flag"></i> @lang('complaints.attributes.status')
                        </label>
                        <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                            <option value="pending" {{ old('status', $complaint->status) == 'pending' ? 'selected' : '' }}>
                                @lang('complaints.status.pending')
                            </option>
                            <option value="in_progress" {{ old('status', $complaint->status) == 'in_progress' ? 'selected' : '' }}>
                                @lang('complaints.status.in_progress')
                            </option>
                            <option value="resolved" {{ old('status', $complaint->status) == 'resolved' ? 'selected' : '' }}>
                                @lang('complaints.status.resolved')
                            </option>
                            <option value="rejected" {{ old('status', $complaint->status) == 'rejected' ? 'selected' : '' }}>
                                @lang('complaints.status.rejected')
                            </option>
                        </select>
                        @error('status')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small d-block mb-2">
                            <i class="fas fa-comment"></i> @lang('complaints.attributes.response_text')
                        </label>
                        <textarea
                            name="response"
                            rows="10"
                            class="form-control @error('response') is-invalid @enderror"
                            placeholder="@lang('complaints.placeholders.response')"
                            required
                        >{{ old('response', $complaint->response) }}</textarea>
                        @error('response')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-paper-plane"></i>
                        @lang('complaints.actions.send_response')
                    </button>
                </form>
            @endcomponent

            {{-- Quick Actions --}}
            @if (auth()->user()?->isAdmin())
                @component('dashboard::components.box')
                    @slot('title')
                        <i class="fas fa-bolt"></i> @lang('complaints.actions.quick_actions')
                    @endslot
                    @slot('class', 'mt-4')

                    @include('dashboard.complaints.partials.actions.delete')
                    @include('dashboard.complaints.partials.actions.restore')
                    @include('dashboard.complaints.partials.actions.forceDelete')
                @endcomponent
            @endif

        </div>
    </div>

</x-layout>
