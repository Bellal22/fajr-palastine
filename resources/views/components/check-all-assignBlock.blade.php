<div class="container">
    @if($people->isNotEmpty())
        @foreach($people as $person)
            <button class="btn btn-outline-info btn-sm"
                    data-checkbox=".item-checkbox"
                    data-toggle="modal"
                    data-target="#assign-selected-modal-{{ $person->id }}">
                <i class="fas fa-location-arrow"></i>
                @lang('check-all.actions.assignBlock')
            </button>

            <!-- Modal لتعيين المندوب -->
            <div class="modal fade" id="assign-selected-modal-{{ $person->id }}" tabindex="-1" role="dialog"
                    aria-labelledby="assign-modal-title-{{ $person->id }}" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="assign-modal-title-{{ $person->id }}">
                                @lang('check-all.dialogs.assign.title')
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        {{ BsForm::open(['url' => route('dashboard.people.assignBlock', $person->id), 'method' => 'PUT', 'id' => 'assign-block-form-' . $person->id]) }}
                        <div class="modal-body">
                            <input type="hidden" name="people_ids" id="selected-people-{{ $person->id }}" value="">
                            {{ BsForm::select('block_id', $blocks)
                                ->placeholder('اختر المندوب')
                                ->required() }}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                                @lang('check-all.dialogs.assign.cancel')
                            </button>
                            <button type="submit" class="btn btn-info btn-sm">
                                @lang('check-all.dialogs.assign.confirm')
                            </button>
                        </div>
                        {{ BsForm::close() }}
                    </div>
                </div>
            </div>
        @endforeach

        <!-- روابط الصفحات -->
        {{ $people->links() }} <!-- هذا سيظهر روابط التنقل بين الصفحات -->
    @else
        <p>لا توجد بيانات لعرضها.</p>
    @endif
</div>
