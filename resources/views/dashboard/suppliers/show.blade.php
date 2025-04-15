<x-layout :title="$supplier->name" :breadcrumbs="['dashboard.suppliers.show', $supplier]">
    <div class="row">
        <div class="col-md-6">
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
                    @if($supplier->getFirstMedia())
                        <tr>
                            <th width="200">@lang('suppliers.attributes.image')</th>
                            <td>
                                <file-preview :media="{{ $supplier->getMediaResource('default') }}"></file-preview>
                            </td>
                        </tr>
                        <tr>
                            <th width="200">@lang('suppliers.attributes.document')</th>
                            <td>
                                <file-preview :media="{{ $supplier->getMediaResource('document') }}"></file-preview>
                            </td>
                        </tr>
                    @endif
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
