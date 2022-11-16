<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scheduled_emails', function (Blueprint $table) {
            $table->id();

            $table->json('recipients');
            $table->longText('mailable');
            $table->string('status')->index();
            $table->unsignedInteger('attempts')->default(0);
            $table->string('error')->nullable();
            $table->text('stacktrace')->nullable();
            $table->dateTime('attempted_at')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('source_type')->nullable();

            $table->index(['status', 'attempts', 'created_at']);

            $table->timestamps();
        });
    }
};
