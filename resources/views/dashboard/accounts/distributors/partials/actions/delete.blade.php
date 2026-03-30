@can('delete', $distributor)
    <a href="#distributor-delete-modal-{{ $distributor->id }}"
       class="btn btn-outline-danger btn-sm"
       data-toggle="modal">
        <i class="fas fa-trash"></i>
    </a>

    <!-- Modal -->
    <div class="modal fade" id="distributor-delete-modal-{{ $distributor->id }}" tabindex="-1" role="dialog"
         aria-labelledby="modal-title-{{ $distributor->id }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title-{{ $distributor->id }}">@lang('distributors.dialogs.delete.title')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-left">
                    {{ trans('distributors.dialogs.delete.info') }}
                </div>
                <div class="modal-footer">
                    {{ BsForm::delete(route('dashboard.distributors.destroy', $distributor)) }}
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        {{ trans('distributors.dialogs.delete.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-danger">
                        {{ trans('distributors.dialogs.delete.confirm') }}
                    </button>
                    {{ BsForm::close() }}
                </div>
            </div>
        </div>
    </div>
@endcan
