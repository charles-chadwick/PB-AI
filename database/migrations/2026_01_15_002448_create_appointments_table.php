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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->string('status')->default('scheduled'); // scheduled, completed, cancelled, no_show
            $table->string('type');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('appointment_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedBigInteger('created_by_id')->default(1);
            $table->unsignedBigInteger('updated_by_id')->nullable();
            $table->unsignedBigInteger('deleted_by_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['appointment_date', 'start_time', 'end_time']);
            $table->index('patient_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
