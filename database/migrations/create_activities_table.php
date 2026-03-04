<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::connection(config('kolaybi.activity-log.connection'))
            ->create(config('kolaybi.activity-log.table', 'activities'), function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->timestamps();
                $table->softDeletes();
                $table->string('creator_id')->nullable()->index();
                $table->string('tenant_id')->nullable()->index();
                $table->string('group')->index();
                $table->string('type')->index();
                $table->json('parameters');
            });
    }

    public function down(): void
    {
        Schema::connection(config('kolaybi.activity-log.connection'))
            ->dropIfExists(config('kolaybi.activity-log.table', 'activities'));
    }
};
