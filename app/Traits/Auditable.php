<?php

namespace App\Traits;

use App\Models\AuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    /**
     * Boot the auditable trait for a model.
     *
     * @return void
     */
    public static function bootAuditable()
    {
        static::created(function (Model $model) {
            $model->auditAction('created');
        });

        static::updated(function (Model $model) {
            $model->auditAction('updated');
        });

        static::deleted(function (Model $model) {
            $model->auditAction('deleted');
        });
    }

    /**
     * Audit a model action.
     *
     * @return void
     */
    protected function auditAction(string $action)
    {
        $oldValues = [];
        $newValues = [];

        if ($action === 'updated') {
            $oldValues = $this->getOriginal();
            $newValues = $this->getDirty();
        } elseif ($action === 'created') {
            $newValues = $this->getAttributes();
        } elseif ($action === 'deleted') {
            $oldValues = $this->getAttributes();
        }

        AuditTrail::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => static::class,
            'model_id' => $this->getKey(),
            'old_values' => empty($oldValues) ? null : $oldValues,
            'new_values' => empty($newValues) ? null : $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
