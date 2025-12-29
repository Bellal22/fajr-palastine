<x-layout :title="$supplier->name" :breadcrumbs="['dashboard.suppliers.show', $supplier]">
    <div class="row">
        <div class="col-md-8">
            @component('dashboard::components.box')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle">
                    <tbody>
                    <tr>
                        <th width="200">@lang('suppliers.attributes.name')</th>
                        <td>{{ $supplier->name }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('suppliers.attributes.description')</th>
                        <td>{{ $supplier->description }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('suppliers.attributes.type')</th>
                        <td>
                            @if($supplier->type == 'donor')
                                <span class="badge badge-success">جهة مانحة</span>
                            @else
                                <span class="badge badge-info">جهة مشغلة</span>
                            @endif
                        </td>
                    </tr>
                    @if($supplier->image)
                        <tr>
                            <th width="200">@lang('suppliers.attributes.image')</th>
                            <td>
                                <img src="{{ asset('storage/' . $supplier->image) }}"
                                     alt="{{ $supplier->name }}"
                                     style="max-width: 100%; max-height: 400px;"
                                     class="img-thumbnail">
                            </td>
                        </tr>
                    @endif
                    @if($supplier->document)
                        <tr>
                            <th width="200">@lang('suppliers.attributes.document')</th>
                            <td>
                                <a href="{{ asset('storage/' . $supplier->document) }}"
                                   target="_blank"
                                   class="btn btn-primary">
                                    <i class="fas fa-file-download"></i> تحميل الملف
                                </a>
                                <small class="text-muted d-block mt-2">
                                    {{ basename($supplier->document) }}
                                </small>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <th width="200">@lang('suppliers.attributes.created_at')</th>
                        <td>{{ $supplier->created_at->format('Y-m-d h:i A') }}</td>
                    </tr>
                    </tbody>
                </table>

                @slot('footer')
                    @include('dashboard.suppliers.partials.actions.edit')
                    @include('dashboard.suppliers.partials.actions.delete')
                    @include('dashboard.suppliers.partials.actions.restore')
                    @include('dashboard.suppliers.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>
</x-layout>
