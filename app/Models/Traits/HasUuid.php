<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    /**
     * implement UUID
     */
    public function getKeyType()
    {
        return 'string';
    }

    protected static function bootHasUuid()
    {
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Str::uuid()->toString();
        });
    }

    public function getIncrementing()
    {
        return false;
    }
}