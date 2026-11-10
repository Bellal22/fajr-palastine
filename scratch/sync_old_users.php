<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Permission;

$allPermissions = config('permission.supported');

// Create permissions in DB if not exist
foreach ($allPermissions as $permName) {
    Permission::findOrCreate($permName, 'web');
}

// Give all permissions to existing admins and supervisors who currently have 0 or full access
$oldUsers = User::whereIn('type', [User::ADMIN_TYPE, User::SUPERVISOR_TYPE])->get();

foreach ($oldUsers as $user) {
    if ($user->permissions()->count() === 0 || $user->isAdmin()) {
        $user->syncPermissions($allPermissions);
        echo "Granted all permissions to: " . $user->name . " (ID: {$user->id})\n";
    }
}

echo "Done!\n";
