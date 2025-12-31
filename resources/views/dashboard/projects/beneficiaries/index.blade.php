<x-layout :title="'المستفيدين من مشروع: ' . $project->name" :breadcrumbs="['dashboard.projects.beneficiaries', $project]">


    @if(session('import_errors'))
        <div class="alert alert-warning">
            <strong>أخطاء الاستيراد:</strong>
            <ul class="mb-0">
                @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- البحث والفلتر --}}
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard.projects.beneficiaries', $project) }}">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label for="search"><i class="fas fa-search"></i> بحث برقم الهوية:</label>
                                <input type="text"
                                       name="search"
                                       id="search"
                                       class="form-control"
                                       placeholder="رقم الهوية..."
                                       value="{{ request('search') }}">
                            </div>


                            <div class="col-md-2 mb-2">
                                <label for="status"><i class="fas fa-filter"></i> الحالة:</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">الكل</option>
                                    <option value="مستلم" {{ request('status') === 'مستلم' ? 'selected' : '' }}>مستلم</option>
                                    <option value="غير مستلم" {{ request('status') === 'غير مستلم' ? 'selected' : '' }}>غير مستلم</option>
                                </select>
                            </div>


                            <div class="col-md-2 mb-2">
                                <label for="date_from"><i class="fas fa-calendar"></i> التاريخ من:</label>
                                <input type="date"
                                       name="date_from"
                                       id="date_from"
                                       class="form-control"
                                       value="{{ request('date_from') }}">
                            </div>


                            <div class="col-md-2 mb-2">
                                <label for="date_to"><i class="fas fa-calendar"></i> التاريخ إلى:</label>
                                <input type="date"
                                       name="date_to"
                                       id="date_to"
                                       class="form-control"
                                       value="{{ request('date_to') }}">
                            </div>


                            <div class="col-md-3 mb-2">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> بحث
                                </button>


                                @if(request('search') || request('status') || request('date_from') || request('date_to'))
                                    <a href="{{ route('dashboard.projects.beneficiaries', $project) }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> إلغاء
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    @component('dashboard::components.table-box')
        @slot('title')
            المستفيدين ({{ $beneficiaries->total() }})
        @endslot


        @slot('tools')
            <a href="{{ route('dashboard.projects.beneficiaries.filter-areas', $project) }}" class="btn btn-info btn-sm">
                <i class="fas fa-filter"></i> ترشيح حسب المناطق
            </a>
            <a href="{{ route('dashboard.projects.beneficiaries.import', $project) }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-import"></i> استيراد من Excel
            </a>
            <a href="{{ route('dashboard.projects.show', $project) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-right"></i> العودة للمشروع
            </a>
        @endslot


        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 10%;">رقم الهوية</th>
                <th style="width: 20%;">الاسم الرباعي</th>
                <th style="width: 9%;">رقم الجوال</th>
                <th style="width: 12%;">المنطقة</th>
                <th style="width: 6%;">الكمية</th>
                <th style="width: 9%;">الحالة</th>
                <th style="width: 10%;">تاريخ التسليم</th>
                <th style="width: 12%;">الملاحظات</th>
                <th style="width: 8%;">...</th>
            </tr>
        </thead>
        <tbody>
        @forelse($beneficiaries as $index => $beneficiary)
            <tr>
                <td class="text-center">{{ $beneficiaries->firstItem() + $index }}</td>
                <td><strong>{{ $beneficiary->id_num }}</strong></td>
                <td>
                    <a href="{{ route('dashboard.people.show', $beneficiary) }}" class="text-decoration-none">
                        {{ $beneficiary->first_name }} {{ $beneficiary->father_name }} {{ $beneficiary->grandfather_name }} {{ $beneficiary->family_name }}
                    </a>
                </td>
                <td>{{ $beneficiary->phone ?? '-' }}</td>
                <td><small class="text-muted">{{ $beneficiary->neighborhood ?? $beneficiary->current_city ?? '-' }}</small></td>
                <td class="text-center"><strong>{{ $beneficiary->pivot->quantity ?? 1 }}</strong></td>
                <td>
                    @if($beneficiary->pivot->status === 'مستلم')
                        <span class="badge badge-success">مستلم</span>
                    @else
                        <span class="badge badge-warning">غير مستلم</span>
                    @endif
                </td>
                <td>
                    @if($beneficiary->pivot->delivery_date)
                        <small><i class="fas fa-calendar-check text-success"></i> {{ \Carbon\Carbon::parse($beneficiary->pivot->delivery_date)->format('Y-m-d') }}</small>
                    @else
                        <small class="text-muted">-</small>
                    @endif
                </td>
                <td><small>{{ $beneficiary->pivot->notes ?? '-' }}</small></td>
                <td>
                    <button class="btn btn-sm btn-primary"
                            data-toggle="modal"
                            data-target="#statusModal{{ $beneficiary->id }}">
                        <i class="fas fa-edit"></i>
                    </button>


                    <form action="{{ route('dashboard.projects.beneficiaries.destroy', [$project, $beneficiary]) }}"
                          method="POST"
                          style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد؟')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center py-4">
                    @if(request('search') || request('status') || request('date_from') || request('date_to'))
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد نتائج للبحث</p>
                        <a href="{{ route('dashboard.projects.beneficiaries', $project) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> إلغاء البحث
                        </a>
                    @else
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا يوجد مستفيدين</p>
                    @endif
                </td>
            </tr>
        @endforelse
        </tbody>


        @if($beneficiaries->hasPages())
            @slot('footer')
                {{ $beneficiaries->links() }}
            @endslot
        @endif
    @endcomponent


    {{-- Modals --}}
    @foreach($beneficiaries as $beneficiary)
        <div class="modal fade" id="statusModal{{ $beneficiary->id }}" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel{{ $beneficiary->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('dashboard.projects.beneficiaries.update-status', [$project, $beneficiary]) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="statusModalLabel{{ $beneficiary->id }}">تحديث حالة: {{ $beneficiary->first_name }} {{ $beneficiary->family_name }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="status{{ $beneficiary->id }}">حالة الاستلام <span class="text-danger">*</span></label>
                                <select name="status" id="status{{ $beneficiary->id }}" class="form-control" required>
                                    <option value="غير مستلم" {{ $beneficiary->pivot->status === 'غير مستلم' ? 'selected' : '' }}>غير مستلم</option>
                                    <option value="مستلم" {{ $beneficiary->pivot->status === 'مستلم' ? 'selected' : '' }}>مستلم</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="quantity{{ $beneficiary->id }}">الكمية <span class="text-danger">*</span></label>
                                <input type="number"
                                       name="quantity"
                                       id="quantity{{ $beneficiary->id }}"
                                       class="form-control"
                                       value="{{ $beneficiary->pivot->quantity ?? 1 }}"
                                       min="1"
                                       required>
                            </div>
                            <div class="form-group">
                                <label for="delivery_date{{ $beneficiary->id }}">تاريخ التسليم</label>
                                <input type="date"
                                       name="delivery_date"
                                       id="delivery_date{{ $beneficiary->id }}"
                                       class="form-control"
                                       value="{{ $beneficiary->pivot->delivery_date ?? date('Y-m-d') }}">
                            </div>
                            <div class="form-group">
                                <label for="notes{{ $beneficiary->id }}">الملاحظات</label>
                                <textarea name="notes" id="notes{{ $beneficiary->id }}" class="form-control" rows="3">{{ $beneficiary->pivot->notes }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ التغييرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</x-layout>


@push('scripts')
<script>
$(document).ready(function() {
    // Focus على حقل البحث عند تحميل الصفحة
    $('#search').focus();


    // عند فتح الـ Modal، اضبط التاريخ الحالي إذا كان فارغاً
    $('.modal').on('show.bs.modal', function (e) {
        var dateInput = $(this).find('input[type="date"]');
        if (!dateInput.val()) {
            var today = new Date().toISOString().split('T')[0];
            dateInput.val(today);
        }
    });


    // Debug
    $('.modal form').on('submit', function(e) {
        console.log('Form submitting to:', $(this).attr('action'));
        console.log('Form data:', $(this).serialize());
    });
});
</script>
@endpush
