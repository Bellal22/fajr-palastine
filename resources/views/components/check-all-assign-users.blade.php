<button type="button" class="btn btn-outline-info btn-sm"
        data-checkbox=".item-checkbox"
        data-form="assign-blocks-form"
        data-toggle="modal"
        data-target="#assign-blocks-modal"
        style="margin-right: 10px;">
    <i class="fas fa-location-arrow"></i>
    @lang('ربط الفرد')
</button>

<!-- Modal -->
<div class="modal fade" id="assign-blocks-modal" tabindex="-1" role="dialog"
     aria-labelledby="assign-blocks-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assign-blocks-title">
                    @lang('check-all.dialogs.assign.title')
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('dashboard.people.bulk_assign_to_users') }}" method="POST" id="assign-blocks-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="items" id="selected-people" value="">
                    <label>@lang('المشرف')</label>
                    <select id="supervisor-select" name="supervisor_id" class="form-control">
                        <option value="">@lang('اختر مشرف')</option>
                        @foreach(\App\Models\Supervisor::all() as $supervisor)
                            <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                        @endforeach
                    </select>
                    <div id="user-wrapper" style="display:none;" class="mt-3">
                        <label>@lang('المندوب')</label>
                        <select id="user-select" name="user_id" class="form-control">
                            <option value="">@lang('اختر المندوب')</option>
                        </select>
                    </div>

                    <input type="hidden" name="user_id" id="hidden-supervisor">
                    <input type="hidden" name="block_id" id="hidden-user">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('الغاء')</button>
                <button type="submit" form="assign-blocks-form" class="btn btn-info" disabled id="submit-btn">@lang('تم')</button>

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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let supervisorSelect = document.getElementById("supervisor-select");
            let userWrapper = document.getElementById("user-wrapper");
            let userSelect = document.getElementById("user-select");
            let hiddenSupervisor = document.getElementById("hidden-supervisor");
            let hiddenUser = document.getElementById("hidden-user");
            let submitBtn = document.getElementById("submit-btn");

            supervisorSelect.addEventListener("change", function () {
                hiddenSupervisor.value = this.value;
                hiddenUser.value = "";
                userSelect.innerHTML = '<option value="">@lang("اختر المندوب")</option>';
                if (this.value) {
                    userWrapper.style.display = "block";
                    axios.get("{{ route('api.blocks.select') }}", {
                    // axios.get("#", {
                        params: {area_responsible_id: this.value}
                    }).then(res => {
                        res.data.data.forEach(user => {
                            let opt = document.createElement("option");
                            opt.value = user.id;
                            opt.text = user.text;
                            userSelect.appendChild(opt);
                        });
                    });
                } else {
                    userWrapper.style.display = "none";
                }
                checkSubmit();
            });

            userSelect.addEventListener("change", function () {
                hiddenUser.value = this.value;
                checkSubmit();
            });

            function checkSubmit() {
                submitBtn.disabled = !(hiddenSupervisor.value && hiddenUser.value);
            }
        });
    </script>
@endpush
