<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\Supervisor;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->call('media-library:clean');

        $this->call(RolesAndPermissionsSeeder::class);

        $admin = Admin::factory()->createOne([
            'name' => 'Admin',
            'email' => 'admin@demo.com',
            'email_verified_at' => now(),
            'remember_token' => null,
            'id_num' => '123456789',
            'first_name' => 'John',
            'father_name' => 'Doe',
            'grandfather_name' => 'Smith',
            'family_name' => 'Johnson',
            'dob' => '1990-01-01',
            'social_status' => 'Single',
            'city' => 'Gaza',
            'current_city' => 'Gaza',
            'neighborhood' => 'Al-Rimal',
            'landmark' => 'Near Market',
            'housing_type' => 'Apartment',
            'housing_damage_status' => 'Good',
            'employment_status' => 'موظف', // Enum: موظف, عامل, لا يعمل
            'person_status' => 'فعال', // Enum: فعال, غير فعال
            'relatives_count' => 3,
            'has_condition' => false,
            'condition_description' => null,
            'phone' => '111111111',
            'phone_verified_at' => now(),
        ]);

        /** @var Supervisor $supervisor */
        $supervisor = Supervisor::factory()->createOne([
            'name' => 'Supervisor',
            'email' => 'supervisor@demo.com',
            'phone' => '222222222',
            'email_verified_at' => now(),
            'remember_token' => null,
            'id_num' => '123456789',
            'first_name' => 'John',
            'father_name' => 'Doe',
            'grandfather_name' => 'Smith',
            'family_name' => 'Johnson',
            'dob' => '1990-01-01',
            'social_status' => 'Single',
            'city' => 'Gaza',
            'current_city' => 'Gaza',
            'neighborhood' => 'Al-Rimal',
            'landmark' => 'Near Market',
            'housing_type' => 'Apartment',
            'housing_damage_status' => 'Good',
            'employment_status' => 'موظف', // Enum: موظف, عامل, لا يعمل
            'person_status' => 'فعال', // Enum: فعال, غير فعال
            'relatives_count' => 3,
            'has_condition' => false,
            'condition_description' => null,
            'phone_verified_at' => now(),
        ]);
        $supervisor->givePermissionTo([
            'manage.customers',
            'manage.feedback',
        ]);

        $customer = Customer::factory()->createOne([
            'phone' => '333333333',
            'name' => 'Customer',
            'email' => 'customer@demo.com',
            'email_verified_at' => now(),
            'remember_token' => null,
            'id_num' => '123456789',
            'first_name' => 'John',
            'father_name' => 'Doe',
            'grandfather_name' => 'Smith',
            'family_name' => 'Johnson',
            'dob' => '1990-01-01',
            'social_status' => 'Single',
            'city' => 'Gaza',
            'current_city' => 'Gaza',
            'neighborhood' => 'Al-Rimal',
            'landmark' => 'Near Market',
            'housing_type' => 'Apartment',
            'housing_damage_status' => 'Good',
            'employment_status' => 'موظف', // Enum: موظف, عامل, لا يعمل
            'person_status' => 'فعال', // Enum: فعال, غير فعال
            'relatives_count' => 3,
            'has_condition' => false,
            'condition_description' => null,
            'phone_verified_at' => now(),
        ]);

        $this->call([
            DummyDataSeeder::class,
        ]);

        $this->command->table(['ID', 'Name', 'Email', 'Phone', 'Password', 'Type', 'Type Code'], [
            [$admin->id, $admin->name, $admin->email, $admin->phone, 'password', 'Admin', $admin->type],
            [
                $supervisor->id,
                $supervisor->name,
                $supervisor->email,
                $supervisor->phone,
                'password',
                'Supervisor',
                $supervisor->type,
            ],
            [
                $customer->id,
                $customer->name,
                $customer->email,
                $customer->phone,
                'password',
                'Customer',
                $customer->type,
            ],
        ]);
    }
}
