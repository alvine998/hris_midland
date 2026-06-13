<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('selfie_in')->nullable()->after('location_out');
            $table->string('selfie_out')->nullable()->after('selfie_in');
            $table->decimal('gps_accuracy_in', 10, 2)->nullable()->after('selfie_out');
            $table->decimal('gps_accuracy_out', 10, 2)->nullable()->after('gps_accuracy_in');
            $table->boolean('is_mock_location_in')->default(false)->after('gps_accuracy_out');
            $table->boolean('is_mock_location_out')->default(false)->after('is_mock_location_in');
            $table->string('check_in_method')->default('manual')->after('is_mock_location_out');
            $table->ipAddress('ip_address_in')->nullable()->after('check_in_method');
            $table->ipAddress('ip_address_out')->nullable()->after('ip_address_in');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'selfie_in',
                'selfie_out',
                'gps_accuracy_in',
                'gps_accuracy_out',
                'is_mock_location_in',
                'is_mock_location_out',
                'check_in_method',
                'ip_address_in',
                'ip_address_out',
            ]);
        });
    }
};
