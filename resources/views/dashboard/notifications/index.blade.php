{{-- صفحة الإشعارات --}}
<x-layout :title="'جميع الإشعارات'" :breadcrumbs="['dashboard.home']">

    {{-- Header Actions --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0"><i class="fas fa-bell mr-1"></i> الإشعارات</h5>
        @if(auth()->user()->unreadNotifications()->count() > 0)
            <form action="{{ route('dashboard.notifications.mark_all_read') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-check-double"></i> تعيين الكل كمقروء
                </button>
            </form>
        @endif
    </div>

    <div class="card card-primary card-outline card-outline-tabs">
        <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-tabs" id="notification-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ !$tab ? 'active' : '' }}" href="{{ route('dashboard.notifications.index') }}">الكل</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $tab == 'people' ? 'active' : '' }}" href="{{ route('dashboard.notifications.index', ['tab' => 'people']) }}">الأفراد</a>
                </li>
                @if(auth()->user()?->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link {{ $tab == 'need_requests' ? 'active' : '' }}" href="{{ route('dashboard.notifications.index', ['tab' => 'need_requests']) }}">طلبات الاحتياج</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $tab == 'stock' ? 'active' : '' }}" href="{{ route('dashboard.notifications.index', ['tab' => 'stock']) }}">المخزن</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $tab == 'supervisors' ? 'active' : '' }}" href="{{ route('dashboard.notifications.index', ['tab' => 'supervisors']) }}">المشرفين</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $tab == 'area_responsibles' ? 'active' : '' }}" href="{{ route('dashboard.notifications.index', ['tab' => 'area_responsibles']) }}">مسؤولي المناطق</a>
                    </li>
                @endif
            </ul>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th style="width: 180px">التاريخ</th>
                            <th>التنبيه</th>
                            <th style="width: 100px">الحالة</th>
                            <th style="width: 150px" class="text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $notification)
                            <tr class="{{ $notification->unread() ? 'table-warning' : '' }}">
                                <td class="align-middle small text-muted">
                                    <i class="far fa-clock mr-1"></i>
                                    {{ $notification->created_at->format('Y-m-d H:i') }}
                                    <br>
                                    <span class="text-xs">{{ $notification->created_at->diffForHumans() }}</span>
                                </td>
                                <td class="align-middle">
                                    <a href="{{ route('dashboard.notifications.show', $notification->id) }}" class="text-decoration-none text-dark">
                                        <div class="font-weight-bold mb-1">
                                            @php
                                                $icon = 'fas fa-bell';
                                                $type = $notification->data['type'] ?? '';
                                                if(str_contains($type, 'person')) $icon = 'fas fa-user text-primary';
                                                if(str_contains($type, 'need')) $icon = 'fas fa-hand-holding-heart text-danger';
                                                if(str_contains($type, 'stock')) $icon = 'fas fa-warehouse text-secondary';
                                                if($type == 'bulk_sync_completed') $icon = 'fas fa-sync-alt text-success';
                                                if($type == 'people_approved') $icon = 'fas fa-check-circle text-success';
                                                if($type == 'delegate_updated') $icon = 'fas fa-user-tie text-info';
                                            @endphp
                                            <i class="{{ $icon }} mr-1 small"></i>
                                            {{ $notification->data['title'] ?? 'تنبيه' }}
                                        </div>
                                        <div class="text-sm text-muted">
                                            {{ $notification->data['message'] ?? '' }}
                                        </div>
                                    </a>
                                </td>
                                <td class="align-middle text-center">
                                    @if($notification->unread())
                                        <span class="badge badge-warning">جديد</span>
                                    @else
                                        <span class="badge badge-light border">مقروء</span>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('dashboard.notifications.show', $notification->id) }}" class="btn btn-xs btn-outline-primary" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i> انتقال
                                        </a>
                                        @if($notification->unread())
                                            <form action="{{ route('dashboard.notifications.mark_as_read', $notification->id) }}" method="POST" class="ml-1 d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-xs btn-outline-success" title="تمييز كمقروء">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fas fa-bell-slash fa-3x mb-3 d-block opacity-50"></i>
                                    لا يوجد إشعارات لعرضها في هذا القسم.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    عرض إشعارات {{ $notifications->firstItem() ?? 0 }} إلى {{ $notifications->lastItem() ?? 0 }} من أصل {{ $notifications->total() }}
                </div>
                @if($notifications->hasPages())
                    <div>
                        {{ $notifications->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout>
