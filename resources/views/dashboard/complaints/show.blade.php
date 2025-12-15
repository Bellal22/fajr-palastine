<x-layout :title="$complaint->complaint_title" :breadcrumbs="['dashboard.complaints.show', $complaint]">
    <div class="row">
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle">
                    <tbody>
                    <tr>
                        <th width="200">@lang('complaints.attributes.id')</th>
                        <td>{{ $complaint->id }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('complaints.attributes.id_num')</th>
                        <td>{{ $complaint->id_num }}</td>
                    </tr>

                    @if($complaint->person)
                    <tr>
                        <th width="200">رقم الجوال</th>
                        <td>{{ $complaint->person->phone ?? 'غير متوفر' }}</td>
                    </tr>
                    <tr>
                        <th width="200">اسم مقدم الشكوى</th>
                        <td>
                            {{ $complaint->person->first_name ?? 'غير متوفر' }}
                            {{ $complaint->person->father_name }}
                            {{ $complaint->person->grandfather_name }}
                            {{ $complaint->person->family_name }}
                        </td>
                    </tr>
                    @endif

                    <tr>
                        <th width="200">@lang('complaints.attributes.complaint_title')</th>
                        <td>{{ $complaint->complaint_title }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('complaints.attributes.complaint_text')</th>
                        <td>{{ $complaint->complaint_text }}</td>
                    </tr>

                    <tr>
                        <th width="200">حالة الشكوى</th>
                        <td>
                            @php
                                $statusColors = [
                                    'pending' => 'warning',
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
                            <span class="badge badge-{{ $statusColors[$complaint->status] ?? 'secondary' }}">
                                {{ $statusLabels[$complaint->status] ?? $complaint->status }}
                            </span>
                        </td>
                    </tr>

                    @if($complaint->response)
                    <tr>
                        <th width="200">الرد</th>
                        <td>{{ $complaint->response }}</td>
                    </tr>
                    <tr>
                        <th width="200">تاريخ الرد</th>
                        <td>{{ $complaint->responded_at?->format('Y-m-d H:i') }}</td>
                    </tr>
                    @if($complaint->responder)
                    <tr>
                        <th width="200">المسؤول عن الرد</th>
                        <td>{{ $complaint->responder->name }}</td>
                    </tr>
                    @endif
                    @endif

                    <tr>
                        <th width="200">تاريخ التقديم</th>
                        <td>{{ $complaint->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                    </tbody>
                </table>

                @slot('footer')
                    @include('dashboard.complaints.partials.actions.edit')
                    @include('dashboard.complaints.partials.actions.delete')
                    @include('dashboard.complaints.partials.actions.restore')
                    @include('dashboard.complaints.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>

        {{-- نموذج الرد على الشكوى --}}
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('title', 'الرد على الشكوى')

                <form action="{{ route('dashboard.complaints.respond', $complaint) }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="status">حالة الشكوى <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                            <option value="pending" {{ old('status', $complaint->status) == 'pending' ? 'selected' : '' }}>
                                قيد الانتظار
                            </option>
                            <option value="in_progress" {{ old('status', $complaint->status) == 'in_progress' ? 'selected' : '' }}>
                                قيد المعالجة
                            </option>
                            <option value="resolved" {{ old('status', $complaint->status) == 'resolved' ? 'selected' : '' }}>
                                تم الحل
                            </option>
                            <option value="rejected" {{ old('status', $complaint->status) == 'rejected' ? 'selected' : '' }}>
                                مرفوضة
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="response">نص الرد <span class="text-danger">*</span></label>
                        <textarea
                            name="response"
                            id="response"
                            rows="8"
                            class="form-control @error('response') is-invalid @enderror"
                            placeholder="اكتب رد مفصل على الشكوى..."
                            required
                        >{{ old('response', $complaint->response) }}</textarea>
                        @error('response')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i>
                            إرسال الرد
                        </button>
                    </div>
                </form>
            @endcomponent
        </div>
    </div>
</x-layout>
