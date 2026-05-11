<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dataset extends Model
{
    protected $fillable = [
        'user_id', 'name', 'description',
        'file_path', 'file_name', 'row_count', 'column_count'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function chartConfigs()
{
    return $this->hasMany(ChartConfig::class);
}
}