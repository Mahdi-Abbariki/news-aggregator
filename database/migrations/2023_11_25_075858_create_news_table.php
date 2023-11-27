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
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('source_id')->unique();
            $table->string('title');
            $table->text('summary');
            $table->longText('body');
            $table->string('image');
            $table->string('author'); //author can be another table too. in this case this field must be a foreign key
            $table->string('source');
            $table->string('section_name')->nullable();
            $table->string('source_url');
            $table->dateTime('published_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
