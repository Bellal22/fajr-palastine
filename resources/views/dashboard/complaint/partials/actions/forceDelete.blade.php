@if (auth()->user()?->isAdmin())
    @can('forceDelete', $complaint)
        <a href="#complaint-{{ $complaint->id }}-force-delete-model"
        class="btn btn-outline-danger btn-sm"
        data-toggle="modal">
            <i class="fas fa fa-fw fa-trash"></i>
        </a>

        <!-- Modal -->
        <div class="modal fade" id="complaint-{{ $complaint->id }}-force-delete-model" tabindex="-1" role="dialog"
            aria-labelledby="modal-title-{{ $complaint->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-title-{{ $complaint->id }}">@lang('complaint.dialogs.forceDelete.title')</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @lang('complaint.dialogs.forceDelete.info')
                    </div>
                    <div class="modal-footer">
                        {{ BsForm::delete(route('dashboard.complaint.forceDelete', $complaint)) }}
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            @lang('complaint.dialogs.forceDelete.cancel')
                        </button>
                        <button type="submit" class="btn btn-danger">
                            @lang('complaint.dialogs.forceDelete.confirm')
                        </button>
                        {{ BsForm::close() }}
                    </div>
                </div>
            </div>
        </div>
    @endcan
@endif
