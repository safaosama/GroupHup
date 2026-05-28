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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            // بدل course_id
            $table->foreignId('section_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('created_by')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->boolean('is_random')->default(false);

            $table->timestamps();
        });

        Schema::create('group_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('group_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->unique(['user_id', 'group_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_user');
        Schema::dropIfExists('groups');
    }
};