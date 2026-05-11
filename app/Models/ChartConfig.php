<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChartConfig extends Model
{
    protected $fillable = [
        'dataset_id', 'user_id', 'title',
        'chart_type', 'x_column', 'y_column', 'limit'
    ];

    public function dataset()
    {
        return $this->belongsTo(Dataset::class);
    }
}