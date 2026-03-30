<x-layout :title="'إدارة الموزعين'" :breadcrumbs="['dashboard.mobile_app.users']">
    @component('dashboard::components.table-box')
        @slot('title')
            <i class="fas fa-users-cog"></i> قائمة الموزعين
            <span class="badge badge-primary badge-pill">{{ number_format($users->total()) }}</span>
        @endslot

        @slot('tools')
            <a href="{{ route('dashboard.mobile_app.users.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-user-plus"></i> إضافة موزع جديد
            </a>
        @endslot

        <thead>
        <tr class="bg-light">
            <th>الموزع</th>
            <th>البريد الإلكتروني</th>
            <th>نوع الحساب</th>
            <th>تاريخ التسجيل</th>
            <th class="text-center" style="width: 100px">حالة المزامنة</th>
        </tr>
        </thead>

        <tbody>
        @foreach($users as $user)
            <tr class="align-middle">
                <td>
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-40px mr-3">
                            <div class="symbol-label bg-light-primary text-primary fw-bold">
                                {{ mb_substr($user->name, 0, 1) }}
                            </div>
                        </div>
                        <span class="text-dark fw-bold text-hover-primary fs-6">{{ $user->name }}</span>
                    </div>
                </td>
                <td>
                    <span class="text-muted fw-semibold">{{ $user->email }}</span>
                </td>
                <td>
                    @php
                        $typeColor = match($user->type) {
                            'admin' => 'secondary',
                            'mobile_worker' => 'success',
                            'supervisor' => 'primary',
                            default => 'dark'
                        };
                    @endphp
                    <span class="badge badge-{{ $typeColor }} fw-bold px-3 py-2">{{ trans('users.types.'.$user->type) }}</span>
                </td>
                <td>
                    <span class="text-muted fs-7">{{ $user->created_at->format('Y/m/d') }}</span>
                </td>
                <td class="text-center">
                    <form action="{{ route('dashboard.mobile_app.toggle_user', $user->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-{{ $user->can_access_mobile_app ? 'success' : 'secondary' }}" title="تفعيل/تعطيل">
                            @if($user->can_access_mobile_app)
                                <i class="fas fa-toggle-on fa-lg"></i>
                            @else
                                <i class="fas fa-toggle-off fa-lg"></i>
                            @endif
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>

        @if($users->hasPages())
            @slot('footer')
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        عرض {{ $users->firstItem() ?? 0 }} إلى {{ $users->lastItem() ?? 0 }} من أصل {{ number_format($users->total()) }}
                    </div>
                    {{ $users->links() }}
                </div>
            @endslot
        @endif
    @endcomponent
</x-layout>
