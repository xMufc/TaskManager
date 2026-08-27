<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ImportResult extends Model
{
    use HasUuids;

    protected $fillable = ['user_id', 'status', 'accepted', 'rejected'];

    protected function casts(): array
    {
        return [
            'accepted' => 'array',
            'rejected' => 'array',
        ];
    }
}
