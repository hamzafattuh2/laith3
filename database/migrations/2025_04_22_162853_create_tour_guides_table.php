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
        Schema::create('tour_guides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('languages'); // Could also be json() if storing multiple languages
            $table->integer('years_of_experience');
            $table->string('license_picture_path'); // Path to stored license image (png)
            $table->string('cv_path'); // Path to stored CV file (pdf)
            $table->string('guide_picture_path'); // Path to guide's profile picture
            $table->rememberToken(); // Important for authentication
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_guides');
    }
};
