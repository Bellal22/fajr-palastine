<button type="button" class="btn btn-outline-info btn-sm"
        data-checkbox=".item-checkbox"
        data-form="assign-blocks-form"
        data-toggle="modal"
        data-target="#assign-blocks-modal"
        style="margin-right: 10px;">
    <i class="fas fa-location-arrow"></i>
    @lang('check-all.actions.assignBlock')
</button>

<!-- Modal -->
<div class="modal fade" id="assign-blocks-modal" tabindex="-1" role="dialog"
     aria-labelledby="assign-blocks-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assign-blocks-title">
                    @lang('check-all.dialogs.assignBlock.title')
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('dashboard.people.assignBlocks') }}" method="POST" id="assign-blocks-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="items" id="selected-people" value="">
                    {{ BsForm::select('block_id', $blocks->toArray())
                        ->placeholder(trans('check-all.dialogs.assignBlock.select_block'))
                        ->label(trans('check-all.dialogs.assignBlock.block_label'))
                        ->required() }}
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    @lang('check-all.dialogs.assignBlock.cancel')
                </button>
                <button type="submit" class="btn btn-info btn-sm" form="assign-blocks-form">
                    @lang('check-all.dialogs.assignBlock.confirm')
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.getElementById('bulk-assign-btn').addEventListener('click', function() {
            const selectedIds = [];
            document.querySelectorAll('.item-checkbox:checked').forEach(checkbox => {
                selectedIds.push(checkbox.value);
            });
            document.getElementById('selected-people').value = selectedIds.join(',');
        });
    </script>
@endpush
