<!-- resources/views/dashboard/area_responsibles/partials/form.blade.php -->

@include('dashboard.errors')

<div class="row">
</div>

<div class="row">
    <div class="col-md-12">
        {{-- حقل الاسم (Name) --}}
        {{ BsForm::text('name')
            ->label(trans('area_responsibles.attributes.name'))
            ->autofocus()
            ->required() }}
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        {{-- حقل رقم الهاتف (Phone) --}}
        {{ BsForm::text('phone')
            ->label(trans('area_responsibles.attributes.phone'))
            }} {{-- لا يوجد required() لأنه nullable في DB --}}
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        {{-- حقل العنوان (Address) --}}
        {{ BsForm::textarea('address')
            ->label(trans('area_responsibles.attributes.address'))
            }} {{-- لا يوجد required() لأنه nullable في DB --}}
    </div>
</div>
