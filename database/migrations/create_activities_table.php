<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::connection(config('kolaybi.activity-log.connection'))
            ->create(config('kolaybi.activity-log.table', 'activities'), function (Blueprint $table) {
                $creatorColumn = config('kolaybi.activity-log.columns.creator', 'creator_id');
                $tenantColumn = config('kolaybi.activity-log.columns.tenant', 'tenant_id');

                $table->ulid('id')->primary();
                $table->timestamps();
                $table->softDeletes();
                $table->string($creatorColumn)->nullable()->index();
                $table->string($tenantColumn)->nullable()->index();
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
