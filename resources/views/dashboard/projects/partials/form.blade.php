@include('dashboard.errors')

<div class="row">
    <div class="col-md-12">
        {{ BsForm::text('name')
            ->label(trans('projects.attributes.name'))
            ->required()
        }}
    </div>
    <div class="col-md-12">
        {{ BsForm::textarea('description')
            ->rows(3)
            ->label(trans('projects.attributes.description'))
        }}
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="start_date">{{ trans('projects.attributes.start_date') }}</label>
            <input type="text" name="start_date" id="start_date" class="form-control datepicker" value="{{ old('start_date', $project->start_date ?? '') }}" autocomplete="off">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="end_date">{{ trans('projects.attributes.end_date') }}</label>
            <input type="text" name="end_date" id="end_date" class="form-control datepicker" value="{{ old('end_date', $project->end_date ?? '') }}" autocomplete="off">
        </div>
    </div>
    <div class="col-md-4">
        {{ BsForm::select('status')
            ->options([
                'active' => 'نشط',
                'completed' => 'مكتمل',
                'suspended' => 'معلق'
            ])
            ->label(trans('projects.attributes.status'))
            ->value(old('status', $project->status ?? 'active'))
        }}
    </div>
</div>

<hr>

<h5 class="mb-3">الشركاء</h5>
<div class="row">
    <div class="col-md-6">
        {{ BsForm::select('granting_entities[]')
            ->label('الجهات المانحة')
            ->options(App\Models\Supplier::where('type', 'organization')->pluck('name', 'id'))
            ->multiple()
            ->attribute('class', 'form-control select2')
            ->value(isset($project) ? $project->grantingEntities->pluck('id')->toArray() : [])
        }}
    </div>

    <div class="col-md-6">
        {{ BsForm::select('executing_entities[]')
            ->label('الجهات المنفذة')
            ->options(App\Models\Supplier::where('type', 'organization')->pluck('name', 'id'))
            ->multiple()
            ->attribute('class', 'form-control select2')
            ->value(isset($project) ? $project->executingEntities->pluck('id')->toArray() : [])
        }}
    </div>
</div>

<hr>

<h5 class="mb-3">الطرود</h5>
<div class="row">
    <div class="col-md-6">
        {{ BsForm::select('ready_packages[]')
            ->label('الطرود الجاهزة')
            ->options(App\Models\ReadyPackage::pluck('name', 'id'))
            ->multiple()
            ->attribute('class', 'form-control select2')
            ->value(isset($project) ? $project->readyPackages->pluck('id')->toArray() : [])
        }}
    </div>

    <div class="col-md-6">
        {{ BsForm::select('internal_packages[]')
            ->label('الطرود الداخلية')
            ->options(App\Models\InternalPackage::pluck('name', 'id'))
            ->multiple()
            ->attribute('class', 'form-control select2')
            ->value(isset($project) ? $project->internalPackages->pluck('id')->toArray() : [])
        }}
    </div>
</div>

<hr>

<h5 class="mt-3 mb-3">أنواع الكوبونات والكميات</h5>
<div id="coupon_types_container">
    @php
        $couponTypes = App\Models\CouponType::all();
        $storedCouponTypes = isset($project) ? $project->couponTypes : collect();
    @endphp

    @if($storedCouponTypes->count() > 0)
        @foreach($storedCouponTypes as $index => $type)
            <div class="row mb-2 type-row">
                <div class="col-md-6">
                    <select name="coupon_types[{{ $index }}][coupon_type_id]" class="form-control" required>
                        <option value="">-- اختر النوع --</option>
                        @foreach($couponTypes as $ctype)
                            <option value="{{ $ctype->id }}" {{ $type->id == $ctype->id ? 'selected' : '' }}>{{ $ctype->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="number" name="coupon_types[{{ $index }}][quantity]" class="form-control" placeholder="الكمية" value="{{ $type->pivot->quantity }}" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-block" onclick="removeTypeRow(this)"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        @endforeach
    @else
        <div class="row mb-2 type-row">
            <div class="col-md-6">
                <select name="coupon_types[0][coupon_type_id]" class="form-control" required>
                    <option value="">-- اختر النوع --</option>
                    @foreach($couponTypes as $ctype)
                        <option value="{{ $ctype->id }}">{{ $ctype->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <input type="number" name="coupon_types[0][quantity]" class="form-control" placeholder="الكمية" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-block" onclick="removeTypeRow(this)"><i class="fas fa-trash"></i></button>
            </div>
        </div>
    @endif
</div>
<button type="button" class="btn btn-success btn-sm mt-2" onclick="addTypeRow()"><i class="fas fa-plus"></i> إضافة نوع</button>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>

<script>
    let typeIndex = {{ isset($project) ? $project->couponTypes->count() : 1 }};

    function addTypeRow() {
        const container = document.getElementById('coupon_types_container');
        const firstRowSelect = document.querySelector('select[name^="coupon_types[0][coupon_type_id]"]');
        const couponOptions = firstRowSelect ? firstRowSelect.innerHTML : '';

        const newRow = `
            <div class="row mb-2 type-row">
                <div class="col-md-6">
                    <select name="coupon_types[${typeIndex}][coupon_type_id]" class="form-control" required>
                        ${couponOptions}
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="number" name="coupon_types[${typeIndex}][quantity]" class="form-control" placeholder="الكمية" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-block" onclick="removeTypeRow(this)"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newRow);
        typeIndex++;
    }

    function removeTypeRow(button) {
        if(document.querySelectorAll('.type-row').length > 1) {
             button.closest('.type-row').remove();
        } else {
            alert('يجب إضافة نوع واحد على الأقل');
        }
    }

    $(document).ready(function() {
        // Initialize Flatpickr
        flatpickr('.datepicker', {
            locale: 'ar',
            dateFormat: 'Y-m-d',
            allowInput: true,
            altInput: true,
            altFormat: 'F j, Y'
        });

        // Initialize Select2
        if ($.fn.select2) {
            $('.select2').select2({
                placeholder: "-- اختر --",
                allowClear: true,
                width: '100%',
                dir: 'rtl'
            });
        }
    });
</script>
@endpush
