<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Supplier;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\SupplierRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * SupplierController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Supplier::class, 'supplier');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $suppliers = Supplier::filter()->latest()->paginate();

        return view('dashboard.suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\SupplierRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SupplierRequest $request)
    {
        // Debug: شوف شو واصل
        Log::info('Request all data:', $request->all());
        Log::info('Has image file?', ['result' => $request->hasFile('image')]);
        Log::info('Has document file?', ['result' => $request->hasFile('document')]);

        $data = $request->only(['name', 'description', 'type']);

        // حفظ الصورة
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            Log::info('Image file details:', [
                'original_name' => $imageFile->getClientOriginalName(),
                'size' => $imageFile->getSize(),
                'mime' => $imageFile->getMimeType(),
            ]);

            $imagePath = $imageFile->store('suppliers/images', 'public');
            Log::info('Image stored at:', ['path' => $imagePath]);
            Log::info('Full storage path:', ['full_path' => storage_path('app/public/' . $imagePath)]);
            Log::info('File exists?', ['exists' => file_exists(storage_path('app/public/' . $imagePath))]);

            $data['image'] = $imagePath;
        } else {
            Log::warning('No image file received');
        }

        // حفظ المستند
        if ($request->hasFile('document')) {
            $documentFile = $request->file('document');
            Log::info('Document file details:', [
                'original_name' => $documentFile->getClientOriginalName(),
                'size' => $documentFile->getSize(),
                'mime' => $documentFile->getMimeType(),
            ]);

            $documentPath = $documentFile->store('suppliers/documents', 'public');
            Log::info('Document stored at:', ['path' => $documentPath]);
            Log::info('Full storage path:', ['full_path' => storage_path('app/public/' . $documentPath)]);
            Log::info('File exists?', ['exists' => file_exists(storage_path('app/public/' . $documentPath))]);

            $data['document'] = $documentPath;
        } else {
            Log::warning('No document file received');
        }

        $supplier = Supplier::create($data);

        flash()->success(trans('suppliers.messages.created'));
        return redirect()->route('dashboard.suppliers.show', $supplier);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Supplier $supplier
     * @return \Illuminate\Http\Response
     */
    public function show(Supplier $supplier)
    {
        return view('dashboard.suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Supplier $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit(Supplier $supplier)
    {
        return view('dashboard.suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\SupplierRequest $request
     * @param \App\Models\Supplier $supplier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->all());

        $supplier->addAllMediaFromTokens();

        flash()->success(trans('suppliers.messages.updated'));

        return redirect()->route('dashboard.suppliers.show', $supplier);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Supplier $supplier
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        flash()->success(trans('suppliers.messages.deleted'));

        return redirect()->route('dashboard.suppliers.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', Supplier::class);

        $suppliers = Supplier::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.suppliers.trashed', compact('suppliers'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\Supplier $supplier
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(Supplier $supplier)
    {
        $this->authorize('viewTrash', $supplier);

        return view('dashboard.suppliers.show', compact('supplier'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\Supplier $supplier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Supplier $supplier)
    {
        $this->authorize('restore', $supplier);

        $supplier->restore();

        flash()->success(trans('suppliers.messages.restored'));

        return redirect()->route('dashboard.suppliers.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\Supplier $supplier
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(Supplier $supplier)
    {
        $this->authorize('forceDelete', $supplier);

        $supplier->forceDelete();

        flash()->success(trans('suppliers.messages.deleted'));

        return redirect()->route('dashboard.suppliers.trashed');
    }
}