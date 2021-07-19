<?php

namespace App\Traits;

use App\Helpers\StringGenericHelper;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

trait ColorAttributeTrait
{

    /**
     * Sets a custom RGBA color for a team
     * This is mainly used for visuals
     *
     * @return string
     */
    public function getColorAttribute()
    {
        return StringGenericHelper::generateRgbaString(1);
    }
}
