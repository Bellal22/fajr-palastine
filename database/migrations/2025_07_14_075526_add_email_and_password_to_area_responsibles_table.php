<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('area_responsibles', function (Blueprint $table) {
                $table->string('email')->unique()->nullable()->after('name');
                $table->string('password')->nullable()->after('email');
            });


        $areaResponsiblesToUpdate = DB::table('area_responsibles')
            ->whereNull('email')
            ->orWhere('email', '')
            ->get();

        foreach ($areaResponsiblesToUpdate as $areaResponsible) {
            $baseEmail = 'user_' . $areaResponsible->id;
            $email = $baseEmail . '@fajeryouth.com';
            $counter = 1;

            while (DB::table('area_responsibles')->where('email', $email)->exists()) {
                $counter++;
                $email = $baseEmail . '_' . $counter . '@fajeryouth.com';
            }

            $password = bcrypt($baseEmail);

            DB::table('area_responsibles')
                ->where('id', $areaResponsible->id)
                ->update([
                    'email' => $email,
                    'password' => $password,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('area_responsibles', function (Blueprint $table) {
            $table->dropColumn('email');
            $table->dropColumn('password');
        });
    }
};
