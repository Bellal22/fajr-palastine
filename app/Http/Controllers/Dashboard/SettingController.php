<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Setting;
use App\Support\SettingJson;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Laraeast\LaravelSettings\Facades\Settings;

class SettingController extends Controller
{
    /**
     * The list of the files keys.
     *
     * @var array
     */
    protected $files = [
        'logo',
        'favicon',
    ];

    /**
     * Display the settings page.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        if (! $tab = request('tab')) {
            return redirect()->route('dashboard.settings.index', ['tab' => 'main']);
        }

        $this->authorize('manage', Setting::class);

        if (! view()->exists($view = "dashboard.settings.tabs.$tab")) {
            abort(404);
        }

        return view($view);
    }

    /**
     * Update the website global settings.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        foreach (
            $request->except(
                array_merge(['_token', '_method', 'media'], $this->files)
            )
            as $key => $value
        ) {
            Settings::set($key, $value);
        }

        foreach ($this->files as $file) {
            Settings::set($file)->addAllMediaFromTokens([], $file);
        }

        app(SettingJson::class)->update();

        flash()->success(trans('settings.messages.updated'));

        return back();
    }

    /**
     * Download a fresh database and storage backup.
     *
     * @throws \Illuminate\Validation\ValidationException
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadBackup()
    {
        Artisan::call('backup:run');

        $lastBackup = collect(Storage::disk('local')->files('laravel-backup'))->last();

        if (! $lastBackup) {
            throw ValidationException::withMessages([
                'backup' => trans('backup.not-found'),
            ]);
        }

        return response()
            ->download(
                Storage::disk('local')->path($lastBackup)
            )
            ->deleteFileAfterSend();
    }

    /**
     * Test the connection to the external API.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function testConnection()
    {
        $apiUrl = config('services.external_api.url');
        $token = config('services.external_api.token');

        if (!$apiUrl) {
            return response()->json([
                'status' => 'error',
                'message' => 'رابط الـ API غير مهيأ في ملف .env'
            ]);
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'X-API-TOKEN' => $token,
                'Accept' => 'application/json',
            ])->timeout(10)->post($apiUrl, [
                'type' => 'test',
                'message' => 'Test connection from ' . config('app.name'),
                'timestamp' => now()->toDateTimeString(),
            ]);

            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'تم الاتصال بالسيرفر بنجاح!',
                    'response' => $response->json()
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'فشل الاتصال: ' . $response->status(),
                'details' => $response->body()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء الاتصال: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Run a bulk sync for all data to the external API.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkSync()
    {
        try {
            $results = \App\Support\ExternalApiSync::bulkSync();
            
            return response()->json([
                'status' => 'success',
                'message' => "تمت عملية المزامنة بنجاح. (ناجح: {$results['success']}, فشل: {$results['errors']})",
                'results' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء المزامنة الشاملة: ' . $e->getMessage()
            ]);
        }
    }
}
