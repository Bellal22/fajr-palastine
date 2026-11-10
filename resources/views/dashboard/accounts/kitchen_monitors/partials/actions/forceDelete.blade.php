@can('forceDelete', $kitchenMonitor)
    <a href="#kitchen-monitor-{{ $kitchenMonitor->id }}-force-delete-model"
       class="btn btn-outline-danger btn-sm"
       data-toggle="modal">
        <i class="fas fa fa-fw fa-trash"></i>
    </a>

    <!-- Modal -->
    <div class="modal fade" id="kitchen-monitor-{{ $kitchenMonitor->id }}-force-delete-model" tabindex="-1" role="dialog"
         aria-labelledby="modal-title-{{ $kitchenMonitor->id }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title-{{ $kitchenMonitor->id }}">حذف نهائي</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    هل أنت متأكد من حذف هذا الحساب نهائياً؟ لا يمكن التراجع عن هذا الإجراء.
                </div>
                <div class="modal-footer">
                    {{ BsForm::delete(route('dashboard.kitchen_monitors.forceDelete', $kitchenMonitor)) }}
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        إلغاء
                    </button>
                    <button type="submit" class="btn btn-danger">
                        حذف نهائي
                    </button>
                    {{ BsForm::close() }}
                </div>
            </div>
        </div>
    </div>
@endcan
