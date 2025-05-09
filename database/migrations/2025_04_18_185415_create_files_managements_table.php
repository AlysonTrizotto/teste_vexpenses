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
        Schema::create('files_managements', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('original_name');
            $table->string('path');
            $table->tinyInteger('status')->default(0)->comment('0: not processed, 1: success on process, 2: failed on process, 3: in progress');
            $table->string('error')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files_managements');
    }
};
