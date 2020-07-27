<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait Uuidable
{
    protected static function bootUuidable()
    {
        self::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    public function getRouteKeyName() {
        return 'uuid';
    }
}
