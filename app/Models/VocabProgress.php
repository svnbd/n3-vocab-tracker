<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VocabProgress extends Model
{
    protected $table = 'vocab_progress';

    protected $fillable = [
        'user_id',
        'day_number',
        'score',
        'passed',
        'is_completed',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'passed' => 'boolean',
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }
   

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}