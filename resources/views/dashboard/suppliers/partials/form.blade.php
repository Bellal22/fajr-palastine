@include('dashboard.errors')

{{ BsForm::text('name')->label(trans('suppliers.attributes.name')) }}

{{ BsForm::textarea('description')->label(trans('suppliers.attributes.description')) }}

<div class="form-group">
    <label for="type">{{ trans('suppliers.attributes.type') }} <span class="text-danger">*</span></label>
    <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
        <option value="">-- اختر النوع --</option>
        <option value="donor" {{ old('type', $supplier->type ?? '') == 'donor' ? 'selected' : '' }}>
            جهة مانحة
        </option>
        <option value="operator" {{ old('type', $supplier->type ?? '') == 'operator' ? 'selected' : '' }}>
            جهة مشغلة
        </option>
    </select>
    @error('type')
        <span class="invalid-feedback d-block">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label for="image">{{ trans('suppliers.attributes.image') }}</label>
    @isset($supplier->image)
        <div class="mb-2">
            <img src="{{ Storage::url($supplier->image) }}" alt="Current Image" style="max-width: 200px; max-height: 200px;">
        </div>
    @endisset
    <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
    @error('image')
        <span class="invalid-feedback d-block">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label for="document">{{ trans('suppliers.attributes.document') }}</label>
    @isset($supplier->document)
        <div class="mb-2">
            <a href="{{ Storage::url($supplier->document) }}" target="_blank" class="btn btn-sm btn-info">
                <i class="fas fa-file"></i> عرض الملف الحالي
            </a>
        </div>
    @endisset
    <input type="file" name="document" id="document" class="form-control @error('document') is-invalid @enderror" accept=".pdf,.doc,.docx">
    @error('document')
        <span class="invalid-feedback d-block">{{ $message }}</span>
    @enderror
</div>
