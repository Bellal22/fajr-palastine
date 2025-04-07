<button class="btn btn-outline-success btn-sm"
        data-checkbox=".item-checkbox"
        data-form="export-selected-form"
        data-toggle="modal"
        data-target="#export-selected-model">
    <i class="fas fa-file-excel"></i>
    @lang('check-all.actions.export')
</button>

<!-- Modal -->
<div class="modal fade" id="export-selected-model" tabindex="-1" role="dialog"
     aria-labelledby="selected-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="selected-modal-title">
                    @lang('check-all.dialogs.export.title')
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-danger">
                @lang('check-all.dialogs.export.info', ['type' => $resource ?? ''])
                <form action="{{ route('dashboard.people.export.selected') }}"
                      id="export-selected-form"
                      method="get">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type ?? '' }}">
                    <input type="hidden" name="resource" value="{{ $resource ?? '' }}">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    @lang('check-all.dialogs.export.cancel')
                </button>
                <button type="submit" class="btn btn-danger btn-sm" form="export-selected-form">
                    @lang('check-all.dialogs.export.confirm')
                </button>
            </div>
        </div>
    </div>
</div>
