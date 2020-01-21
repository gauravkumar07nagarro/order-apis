<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    public const UNASSIGNED = 'UNASSIGNED';
    public const TAKEN = 'TAKEN';

    protected $fillable = ['origin_lat', 'origin_long', 'dest_lat', 'dest_long', 'distance', 'status', 'created_at', 'updated_at'];

    protected $hidden = ['origin_lat', 'origin_long', 'dest_lat', 'dest_long', 'created_at', 'updated_at'];

    public function getDateFormat()
    {
        return 'U';
    }
}
