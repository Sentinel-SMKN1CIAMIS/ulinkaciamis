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
        Schema::table('locations', function (Blueprint $table) {
            if (! Schema::hasColumn('locations', 'kecamatan')) {
                $table->string('kecamatan')->default('Ciamis')->after('address');
            }
            if (! Schema::hasColumn('locations', 'rating')) {
                $table->decimal('rating', 3, 1)->default(4.8)->after('closing_hours');
            }
            if (! Schema::hasColumn('locations', 'features')) {
                $table->text('features')->nullable()->after('rating');
            }
            if (! Schema::hasColumn('locations', 'moods')) {
                $table->text('moods')->nullable()->after('features');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn(['kecamatan', 'rating', 'features', 'moods']);
        });
    }
};
