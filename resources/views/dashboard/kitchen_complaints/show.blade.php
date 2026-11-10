<x-layout :title="trans('kitchen_complaints.actions.show')" :breadcrumbs="['dashboard.kitchen_complaints.show', $kitchenComplaint]">

    <div class="row">
        {{-- Complaint details --}}
        <div class="col-md-7">
            @component('dashboard::components.box')
                @slot('title')
                    <i class="fas fa-info-circle text-primary"></i> تفاصيل الشكوى
                @endslot

                <table class="table table-bordered table-striped">
                    <tr>
                        <th style="width: 200px;">اسم مقدم الشكوى</th>
                        <td>{{ $kitchenComplaint->complainant_name }}</td>
                    </tr>
                    <tr>
                        <th>اسم المخيم</th>
                        <td>{{ $kitchenComplaint->camp_name }}</td>
                    </tr>
                    <tr>
                        <th>رقم البطاقة</th>
                        <td>{{ $kitchenComplaint->card_number }}</td>
                    </tr>
                    <tr>
                        <th>رقم جوال التواصل</th>
                        <td><a href="tel:{{ $kitchenComplaint->mobile_number }}">{{ $kitchenComplaint->mobile_number }}</a></td>
                    </tr>
                    <tr>
                        <th>عنوان الشكوى</th>
                        <td><strong>{{ $kitchenComplaint->complaint_title }}</strong></td>
                    </tr>
                    <tr>
                        <th>نص الشكوى</th>
                        <td style="white-space: pre-wrap; line-height: 1.6;">{{ $kitchenComplaint->complaint_text }}</td>
                    </tr>
                    <tr>
                        <th>تاريخ الإرسال</th>
                        <td>{{ $kitchenComplaint->created_at->format('Y-m-d H:i') }} ({{ $kitchenComplaint->created_at->diffForHumans() }})</td>
                    </tr>
                    <tr>
                        <th>الحالة الحالية</th>
                        <td>
                            @if($kitchenComplaint->status == 'pending')
                                <span class="badge badge-warning">قيد الانتظار</span>
                            @elseif($kitchenComplaint->status == 'in_progress')
                                <span class="badge badge-info">قيد المعالجة</span>
                            @elseif($kitchenComplaint->status == 'resolved')
                                <span class="badge badge-success">تم الحل</span>
                            @else
                                <span class="badge badge-danger">مرفوض</span>
                            @endif
                        </td>
                    </tr>
                </table>

                @if($kitchenComplaint->response)
                    <div class="alert alert-success mt-4">
                        <h5><i class="fas fa-reply"></i> الرد المسجل:</h5>
                        <p class="mb-2" style="white-space: pre-wrap;">{{ $kitchenComplaint->response }}</p>
                        <hr class="my-2">
                        <small>تم الرد بواسطة: <strong>{{ $kitchenComplaint->responder?->name }}</strong> بتاريخ: {{ $kitchenComplaint->responded_at?->format('Y-m-d H:i') }}</small>
                    </div>
                @endif
            @endcomponent
        </div>

        {{-- Respond Box --}}
        <div class="col-md-5">
            @component('dashboard::components.box')
                @slot('title')
                    <i class="fas fa-reply text-success"></i> متابعة والرد على الشكوى
                @endslot

                <form action="{{ route('dashboard.kitchen_complaints.respond', $kitchenComplaint) }}" method="POST">
                    @csrf
                    
                    <div class="form-group mb-3">
                        <label for="status" class="form-label font-weight-bold">تحديث حالة الشكوى</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="pending" {{ $kitchenComplaint->status == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="in_progress" {{ $kitchenComplaint->status == 'in_progress' ? 'selected' : '' }}>قيد المعالجة</option>
                            <option value="resolved" {{ $kitchenComplaint->status == 'resolved' ? 'selected' : '' }}>تم الحل</option>
                            <option value="rejected" {{ $kitchenComplaint->status == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="response" class="form-label font-weight-bold">نص الرد</label>
                        <textarea name="response" id="response" rows="6" class="form-control" placeholder="اكتب ردك هنا..." required>{{ old('response', $kitchenComplaint->response) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-success btn-block">
                        <i class="fas fa-paper-plane"></i> إرسال الرد وتحديث الحالة
                    </button>
                </form>
            @endcomponent
        </div>
    </div>

</x-layout>
