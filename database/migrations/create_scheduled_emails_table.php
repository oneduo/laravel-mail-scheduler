<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('mail-scheduler.table_name'), function (Blueprint $table) {
            $table->id();
            $table->json('recipients');
            $table->longText('mailable');
            $table->string('status')->index();
            $table->unsignedInteger('attempts')->default(0);
            $table->string('error')->nullable();
            $table->text('stacktrace')->nullable();
            $table->nullableMorphs('source');
            $table->timestamp('attempted_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'attempts', 'created_at']);
        });
    }
};
