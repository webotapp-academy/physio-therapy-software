<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipment';

    protected $fillable = [
        'name',
        'equipment_code',
        'is_portable',
        'current_location',
        'serial_number',
        'purchase_date',
        'last_serviced_date',
        'next_service_due',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_portable' => 'boolean',
            'purchase_date' => 'date',
            'last_serviced_date' => 'date',
            'next_service_due' => 'date',
        ];
    }
}
