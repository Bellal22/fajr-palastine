<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \App\Models\Distributor::class => \App\Policies\DistributorPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::guessPolicyNamesUsing(function ($modelClass) {
            return '\App\Policies\\'.class_basename($modelClass).'Policy';
        });

        $modelPermissionMap = [
            \App\Models\Admin::class => 'manage.admins',
            \App\Models\Supervisor::class => 'manage.supervisors',
            \App\Models\Distributor::class => 'manage.distributors',
            \App\Models\Researcher::class => 'manage.researchers',
            \App\Models\KitchenMonitor::class => 'manage.kitchen_monitors',
            \App\Models\Customer::class => 'manage.customers',
            \App\Models\Feedback::class => 'manage.feedback',
            \App\Models\Setting::class => 'manage.settings',
            \App\Models\Choose::class => 'manage.chooses',
            \App\Models\Family::class => 'manage.families',
            \App\Models\Neighborhood::class => 'manage.neighborhoods',
            \App\Models\City::class => 'manage.cities',
            \App\Models\Person::class => 'manage.people',
            \App\Models\IncompletePerson::class => 'manage.incomplete_people',
            \App\Models\Employee::class => 'manage.employees',
            \App\Models\Complaint::class => 'manage.complaints',
            \App\Models\Supplier::class => 'manage.suppliers',
            \App\Models\AreaResponsible::class => 'manage.area_responsibles',
            \App\Models\Block::class => 'manage.blocks',
            \App\Models\Region::class => 'manage.regions',
            \App\Models\Location::class => 'manage.locations',
            \App\Models\Map::class => 'manage.maps',
            \App\Models\InboundShipment::class => 'manage.inbound_shipments',
            \App\Models\Item::class => 'manage.items',
            \App\Models\ReadyPackage::class => 'manage.ready_packages',
            \App\Models\InternalPackage::class => 'manage.internal_packages',
            \App\Models\PackageContent::class => 'manage.package_contents',
            \App\Models\SubWarehouse::class => 'manage.sub_warehouses',
            \App\Models\OutboundShipment::class => 'manage.outbound_shipments',
            \App\Models\OutboundShipmentItem::class => 'manage.outbound_shipment_items',
            \App\Models\CouponType::class => 'manage.coupon_types',
            \App\Models\Project::class => 'manage.projects',
            \App\Models\CustomProject::class => 'manage.custom_projects',
            \App\Models\RrmBatch::class => 'manage.rrm',
            \App\Models\GameWinning::class => 'manage.game_winnings',
            \App\Models\NeedRequest::class => 'manage.need_requests',
            \App\Models\KitchenCard::class => 'manage.kitchen_cards',
            \App\Models\KitchenComplaint::class => 'manage.kitchen_complaints',
        ];

        Gate::before(function ($user, $ability, $params = []) use ($modelPermissionMap) {
            // Direct permission check
            if (in_array($ability, config('permission.supported'))) {
                return $user->hasPermission($ability);
            }

            // Model policy action check (viewAny, view, create, update, delete, etc.)
            if (!empty($params) && isset($params[0])) {
                $target = $params[0];
                $modelClass = is_object($target) ? get_class($target) : $target;

                if (isset($modelPermissionMap[$modelClass])) {
                    $permission = $modelPermissionMap[$modelClass];
                    return $user->hasPermission($permission);
                }
            }
        });
    }
}
