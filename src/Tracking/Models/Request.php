<?php

namespace TaivasAPM\Tracking\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    protected $table = 'tracking_requests';
    protected $casts = [
        'db_queries' => 'array',
    ];
}
