<x-layout :title="'جميع الإشعارات'" :breadcrumbs="['dashboard.home']">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">قائمة التنبيهات</h3>
            <div class="card-tools">
                <form action="{{ route('dashboard.notifications.mark_all_read') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-primary">تمييز الكل كمقروء</button>
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>التنبيه</th>
                        <th>الحالة</th>
                        <th style="width: 100px">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $notification)
                        <tr class="{{ $notification->unread() ? 'table-warning' : '' }}">
                            <td>{{ $notification->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <strong>{{ $notification->data['title'] ?? 'تنبيه' }}</strong><br>
                                <span class="text-muted">{{ $notification->data['message'] ?? '' }}</span>
                            </td>
                            <td>
                                @if($notification->unread())
                                    <span class="badge badge-warning">جديد</span>
                                @else
                                    <span class="badge badge-light">مقروء</span>
                                @endif
                            </td>
                            <td>
                                @if($notification->unread())
                                    <form action="{{ route('dashboard.notifications.mark_as_read', $notification->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-outline-success">تمييز كمقروء</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">لا يوجد إشعارات لعرضها.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($notifications->hasPages())
            <div class="card-footer clearfix">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</x-layout>
