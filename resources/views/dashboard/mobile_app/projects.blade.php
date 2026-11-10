<x-layout :title="'مزامنة مشاريع الموزعين'" :breadcrumbs="['dashboard.mobile_app.projects']">
    @component('dashboard::components.table-box')
        @slot('title')
            <i class="fas fa-sync-alt"></i> المشاريع المتاحة للمزامنة
            <span class="badge badge-primary badge-pill">{{ number_format($projects->total()) }}</span>
        @endslot

        <thead>
        <tr class="bg-light">
            <th>المشروع</th>
            <th>تاريخ البدء</th>
            <th>الحالة</th>
            <th class="text-center" style="width: 150px">الإجراءات</th>
        </tr>
        </thead>

        <tbody>
        @foreach($projects as $project)
            <tr class="align-middle">
                <td>
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-40px mr-3">
                            <div class="symbol-label bg-light-success text-success">
                                <i class="fas fa-shipping-fast"></i>
                            </div>
                        </div>
                        <a href="{{ route('dashboard.projects.show', $project) }}" class="text-dark fw-bold text-hover-primary">
                            {{ $project->name }}
                        </a>
                    </div>
                </td>
                <td>
                    <span class="text-muted small">{{ $project->start_date ?? '---' }}</span>
                </td>
                <td>
                    <span class="badge badge-success">نشط للمزامنة</span>
                </td>
                <td class="text-center">
                    <div class="btn-group">
                        <a href="{{ route('dashboard.mobile_app.nominate', ['project_id' => $project->id]) }}" class="btn btn-sm btn-info" title="بدء التكليف">
                            <i class="fas fa-hand-pointer"></i> تكليف الموزعين
                        </a>
                        <form action="{{ route('dashboard.mobile_app.projects.refresh', $project->id) }}" method="POST" onsubmit="return confirm('هل تريد إرسال إشارة تحديث لتطبيق الجوال لهذا المشروع؟')" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary" title="تحديث للمشروع (APP)">
                                <i class="fas fa-sync"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>

        @if($projects->hasPages())
            @slot('footer')
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        عرض {{ $projects->firstItem() ?? 0 }} إلى {{ $projects->lastItem() ?? 0 }} من أصل {{ number_format($projects->total()) }}
                    </div>
                    {{ $projects->links() }}
                </div>
            @endslot
        @endif
    @endcomponent
</x-layout>
