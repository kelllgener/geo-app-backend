<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpInfo extends Model
{
    protected $fillable = ["ip_address", "user_id"];

    public function user() {
        return $this->belongsTo(User::class);
    }

}
