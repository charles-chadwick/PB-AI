<?php
/** @noinspection DuplicatedCode */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() : void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')
                ->nullable();
            $table->string('last_name');
            $table->string('email')
                ->unique();
            $table->date('date_of_birth')
                ->default('1900-01-01');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by_id')
                ->default(1);
            $table->unsignedBigInteger('updated_by_id')
                ->nullable();
            $table->unsignedBigInteger('deleted_by_id')
                ->nullable();
        });
    }

    public function down() : void
    {
        Schema::dropIfExists('patients');
    }
};
