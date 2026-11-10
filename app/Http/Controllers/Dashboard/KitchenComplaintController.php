<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\KitchenComplaint;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\KitchenComplaintRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class KitchenComplaintController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->authorizeResource(KitchenComplaint::class, 'kitchen_complaint');
    }

    public function index()
    {
        $complaints = KitchenComplaint::latest()->paginate();
        return view('dashboard.kitchen_complaints.index', compact('complaints'));
    }

    public function show(KitchenComplaint $kitchenComplaint)
    {
        $kitchenComplaint->load('responder');
        return view('dashboard.kitchen_complaints.show', compact('kitchenComplaint'));
    }

    public function respond(KitchenComplaintRequest $request, KitchenComplaint $kitchenComplaint)
    {
        $this->authorize('update', $kitchenComplaint);

        $kitchenComplaint->update([
            'response' => $request->response,
            'status' => $request->status,
            'responded_at' => now(),
            'responded_by' => auth()->id(),
        ]);

        flash()->success(trans('kitchen_complaints.messages.responded'));

        return redirect()->route('dashboard.kitchen_complaints.show', $kitchenComplaint);
    }

    public function destroy(KitchenComplaint $kitchenComplaint)
    {
        $kitchenComplaint->delete();

        flash()->success(trans('kitchen_complaints.messages.deleted'));

        return redirect()->route('dashboard.kitchen_complaints.index');
    }
}
