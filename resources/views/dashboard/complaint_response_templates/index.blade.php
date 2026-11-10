<x-layout title="الردود الجاهزة للشكاوى" :breadcrumbs="['dashboard.complaint_response_templates.index']">
    @component('dashboard::components.table-box')
        @slot('title')
            <i class="fas fa-reply-all"></i> الردود الجاهزة والتلقائية
            <span class="badge badge-primary badge-pill">{{ count_formatted($templates->total()) }}</span>
        @endslot

        <thead>
        <tr>
            <th colspan="100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="m-0 font-weight-bold">قائمة الردود الجاهزة</h5>
                    </div>
                    <div>
                        <a href="{{ route('dashboard.complaint_response_templates.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> إضافة رد جاهز جديد
                        </a>
                    </div>
                </div>
            </th>
        </tr>

        <tr class="bg-light">
            <th style="width: 60px">#</th>
            <th><i class="fas fa-heading"></i> عنوان الرد</th>
            <th><i class="fas fa-align-left"></i> نص الرد التلقائي</th>
            <th><i class="fas fa-flag"></i> الحالة المقترحة للشكوى</th>
            <th><i class="fas fa-calendar"></i> تاريخ الإضافة</th>
            <th class="text-center" style="width: 150px"><i class="fas fa-cog"></i> الإجراءات</th>
        </tr>
        </thead>

        <tbody>
        @forelse($templates as $template)
            <tr class="align-middle">
                <td>{{ $template->id }}</td>
                <td class="font-weight-bold text-primary">{{ $template->title }}</td>
                <td>
                    <span class="text-muted">{{ Str::limit($template->content, 80) }}</span>
                </td>
                <td>
                    @if($template->status)
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
                                'resolved' => 'تم الحل / مقبول',
                                'rejected' => 'مرفوض',
                            ];
                        @endphp
                        <span class="badge badge-{{ $statusColors[$template->status] ?? 'secondary' }}">
                            {{ $statusLabels[$template->status] ?? $template->status }}
                        </span>
                    @else
                        <span class="text-muted small">بدون تغيير الحالة</span>
                    @endif
                </td>
                <td>
                    <small class="text-muted">{{ $template->created_at->format('Y-m-d') }}</small>
                </td>
                <td class="text-center">
                    <a href="{{ route('dashboard.complaint_response_templates.edit', $template) }}" class="btn btn-sm btn-info mr-1">
                        <i class="fas fa-edit"></i> تعديل
                    </a>
                    <form action="{{ route('dashboard.complaint_response_templates.destroy', $template) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت تأكد من حذف هذا الرد الجاهز؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i> حذف
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fas fa-reply-all fa-3x mb-3 d-block text-secondary"></i>
                        <h5>لا توجد ردود جاهزة مضافة حالياً</h5>
                        <p class="small text-muted">يمكنك إضافة ردود جاهزة جديدة واستخدامها بسهولة عند الرد على شكاوى المواطنين.</p>
                        <a href="{{ route('dashboard.complaint_response_templates.create') }}" class="btn btn-primary btn-sm mt-2">
                            <i class="fas fa-plus"></i> إضافة رد جاهز الآن
                        </a>
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>

        @if($templates->hasPages())
            @slot('footer')
                <div class="d-flex justify-content-between align-items-center">
                    {{ $templates->links() }}
                </div>
            @endslot
        @endif
    @endcomponent
</x-layout>
