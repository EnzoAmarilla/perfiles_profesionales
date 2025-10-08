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
            if (Schema::hasColumn('users', 'profile_user_id')) {
                $table->dropForeign(['profile_user_id']);
                $table->dropColumn('profile_user_id');
            }
        });

        if (Schema::hasTable('profiles_users')) {
            Schema::dropIfExists('profiles_users');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('profiles_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('disabled')->default(false);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('profile_user_id')->nullable()->constrained('profiles_users')->nullOnDelete();
        });
    }
};
