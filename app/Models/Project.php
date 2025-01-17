<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'status', 'start_date', 'end_date', 'deleted_at'];

    protected function casts() : array
    {
        return [
            "start_date" => 'datetime',
            "end_date" => 'datetime',
        ];
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'project_id');
    }
}
