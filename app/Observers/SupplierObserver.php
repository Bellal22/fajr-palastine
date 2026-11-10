<?php

namespace App\Observers;

use App\Models\Supplier;
use App\Support\ExternalApiSync;

class SupplierObserver
{
    /**
     * Handle the Supplier "created" event.
     *
     * @param \App\Models\Supplier $supplier
     * @return void
     */
    public function created(Supplier $supplier)
    {
        ExternalApiSync::syncSupplier($supplier);
    }

    /**
     * Handle the Supplier "updated" event.
     *
     * @param \App\Models\Supplier $supplier
     * @return void
     */
    public function updated(Supplier $supplier)
    {
        ExternalApiSync::syncSupplier($supplier);
    }
}
