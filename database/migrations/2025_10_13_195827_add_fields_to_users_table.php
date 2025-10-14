<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Nuevos campos personales
            $table->string('username')->nullable()->after('id');
            $table->string('first_name')->nullable()->after('username');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('document_type')->nullable()->after('last_name');
            $table->string('document_number')->nullable()->after('document_type');
            $table->date('birth_date')->nullable()->after('document_number');
            $table->string('nationality')->nullable()->after('birth_date');

            // Contacto
            $table->string('country_phone')->nullable()->after('nationality');
            $table->string('area_code')->nullable()->after('country_phone');
            $table->string('phone_number')->nullable()->after('area_code');

            $table->string('address')->nullable()->after('locality_id');
            $table->string('street')->nullable()->after('address');
            $table->string('street_number')->nullable()->after('street');
            $table->string('floor')->nullable()->after('street_number');
            $table->string('apartment')->nullable()->after('floor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
           $table->dropColumn([
                'username',
                'first_name',
                'last_name',
                'document_type',
                'document_number',
                'birth_date',
                'nationality',
                'country_phone',
                'area_code',
                'phone_number',
                'address',
                'street',
                'street_number',
                'floor',
                'apartment',
            ]);
        });
    }
};
