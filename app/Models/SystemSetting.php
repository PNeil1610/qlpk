<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $table = 'system_settings';

    protected $fillable = [
        'clinic_name', 'address', 'phone_number', 'email', 
        'logo_url', 'working_hours', 'description'
    ];
}