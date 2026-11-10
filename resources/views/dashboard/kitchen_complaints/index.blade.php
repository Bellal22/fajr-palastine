<x-layout :title="trans('kitchen_complaints.plural')" :breadcrumbs="['dashboard.kitchen_complaints.index']">

    @component('dashboard::components.table-box')
        @slot('title')
            <i class="fas fa-bullhorn"></i> @lang('kitchen_complaints.actions.list')
            <span class="badge badge-primary badge-pill">{{ count_formatted($complaints->total()) }}</span>
        @endslot

        <thead>
        <tr class="bg-light">
            <th><i class="fas fa-user"></i> @lang('kitchen_complaints.attributes.complainant_name')</th>
            <th><i class="fas fa-campground"></i> @lang('kitchen_complaints.attributes.camp_name')</th>
            <th><i class="fas fa-id-card"></i> @lang('kitchen_complaints.attributes.card_number')</th>
            <th><i class="fas fa-heading"></i> @lang('kitchen_complaints.attributes.complaint_title')</th>
            <th><i class="fas fa-phone"></i> @lang('kitchen_complaints.attributes.mobile_number')</th>
            <th><i class="fas fa-cog"></i> @lang('kitchen_complaints.attributes.status')</th>
            <th class="text-center" style="width: 150px">الإجراءات</th>
        </tr>
        </thead>

        <tbody>
        @forelse($complaints as $complaint)
            <tr class="align-middle">
                <td><strong>{{ $complaint->complainant_name }}</strong></td>
                <td>{{ $complaint->camp_name }}</td>
                <td class="text-nowrap">{{ $complaint->card_number }}</td>
                <td><strong>{{ Str::limit($complaint->complaint_title, 50) }}</strong></td>
                <td class="text-nowrap">{{ $complaint->mobile_number }}</td>
                <td>
                    @if($complaint->status == 'pending')
                        <span class="badge badge-warning">قيد الانتظار</span>
                    @elseif($complaint->status == 'in_progress')
                        <span class="badge badge-info">قيد المعالجة</span>
                    @elseif($complaint->status == 'resolved')
                        <span class="badge badge-success">تم الحل</span>
                    @else
                        <span class="badge badge-danger">مرفوض</span>
                    @endif
                </td>
                <td class="text-center">
                    <a href="{{ route('dashboard.kitchen_complaints.show', $complaint) }}" class="btn btn-sm btn-primary" title="عرض ومتابعة">
                        <i class="fas fa-eye"></i>
                    </a>
                    <form action="{{ route('dashboard.kitchen_complaints.destroy', $complaint) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الشكوى؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center text-muted">لا يوجد شكاوى مطبخ مضافة بعد.</td>
            </tr>
        @endforelse
        </tbody>

        @slot('footer')
            {{ $complaints->links() }}
        @endslot
    @endcomponent

</x-layout>
