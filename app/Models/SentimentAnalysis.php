<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SentimentAnalysis extends Model
{
    //use HasFactory, SoftDeletes;

    // The table associated with the model
    protected $table = 'sentiment_analysis';

    // The attributes that are mass assignable
    protected $fillable = [
        'input_text',
        'analysis_result',
        'emotion_detected',
        'feature_data',
        'analysis_date',
    ];

}
