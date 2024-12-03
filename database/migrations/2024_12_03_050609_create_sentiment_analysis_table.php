<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('sentiment_analysis', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->text('input_text'); // Input of user
            $table->string('analysis_result'); // Result of the sentiment
            $table->string('emotion_detected'); // Emotion result
            $table->text('feature_data')->nullable(); // Text Features
            $table->timestamp('analysis_date'); // sentiment_date
            $table->softDeletes(); // Soft delete functionality
            $table->timestamps(); // Created_at and updated_at timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('sentiment_analysis');
    }
};
