<!-- resources/views/dashboard/blocks/partials/form.blade.php -->

@include('dashboard.errors')

<div class="row">
    <div class="col-md-12">
        {{-- حقل الاسم (Name) --}}
        {{ BsForm::text('name')
            ->required() }}
    </div>
</div>

@if(auth()->user()->isAdmin())
<div class="row">
    <div class="col-md-12">
        {{-- حقل مسؤول المنطقة (Area Responsible) كقائمة منسدلة --}}
        {{ BsForm::select('area_responsible_id')
            ->options($areaResponsibles ?? [])
            ->value(isset($block) ? $block->area_responsible_id : request('area_responsible_id'))
            ->label(trans('blocks.attributes.area_responsible'))
            ->placeholder(trans('blocks.placeholders.select_area_responsible')) }}
    </div>
</div>
@endif
<div class="row">
    <div class="col-md-12">
        {{-- حقل العنوان (Title) --}}
        {{ BsForm::text('title')
            ->label(trans('blocks.attributes.title'))
            ->required() }}
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        {{-- حقل رقم الهاتف (Phone) --}}
        {{ BsForm::number('phone')
            ->label(trans('blocks.attributes.phone'))
            ->required() }}
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        {{-- حقل خط الطول (Latitude) --}}
        {{ BsForm::text('lat')
            ->label(trans('blocks.attributes.lat'))
            ->required() }}
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        {{-- حقل خط العرض (Longitude) --}}
        {{ BsForm::text('lan')
            ->label(trans('blocks.attributes.lan'))
            ->required() }}
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        {{-- حقل الملاحظات (Note) --}}
        {{ BsForm::textarea('note')
            ->label(trans('blocks.attributes.note'))
            ->required() }}
    </div>
</div>
