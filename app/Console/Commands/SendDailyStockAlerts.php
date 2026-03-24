<?php

namespace App\Console\Commands;

use App\Models\Item;
use App\Models\ReadyPackage;
use App\Models\InternalPackage;
use App\Models\User;
use App\Notifications\LowStockAlert;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendDailyStockAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:alerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily notifications for low and out-of-stock items';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $lowStockItems = [];
        $outOfStockItems = [];

        // Check Items
        $items = Item::where('remaining_quantity', '<=', \DB::raw('min_stock'))
            ->get();
            
        foreach ($items as $item) {
            $data = [
                'name' => $item->name,
                'type' => 'صنف',
                'quantity' => $item->remaining_quantity,
                'min' => $item->min_stock,
            ];
            if ($item->remaining_quantity <= 0) {
                $outOfStockItems[] = $data;
            } else {
                $lowStockItems[] = $data;
            }
        }

        // Check Ready Packages
        $packages = ReadyPackage::where('remaining_quantity', '<=', \DB::raw('min_stock'))->get();
        foreach ($packages as $package) {
            $data = [
                'name' => $package->name,
                'type' => 'طرد جاهز',
                'quantity' => $package->remaining_quantity,
                'min' => $package->min_stock,
            ];
            if ($package->remaining_quantity <= 0) {
                $outOfStockItems[] = $data;
            } else {
                $lowStockItems[] = $data;
            }
        }

        // Check Internal Packages
        $internalPackages = InternalPackage::where('remaining_quantity', '<=', \DB::raw('min_stock'))->get();
        foreach ($internalPackages as $package) {
            $data = [
                'name' => $package->name,
                'type' => 'طرد داخلي',
                'quantity' => $package->remaining_quantity,
                'min' => $package->min_stock,
            ];
            if ($package->remaining_quantity <= 0) {
                $outOfStockItems[] = $data;
            } else {
                $lowStockItems[] = $data;
            }
        }

        if (empty($lowStockItems) && empty($outOfStockItems)) {
            $this->info('No low stock items found.');
            return;
        }

        // Notify Admins
        $admins = User::where('type', 'admin')->get();
        Notification::send($admins, new LowStockAlert($lowStockItems, $outOfStockItems));

        $this->info('Daily stock alerts sent to ' . $admins->count() . ' admins.');
    }
}
