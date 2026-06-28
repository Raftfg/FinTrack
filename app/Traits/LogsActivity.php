<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait LogsActivity
{
    /**
     * Boot the trait and register Eloquent event listeners.
     */
    protected static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            $user = auth()->user();
            $userInfo = $user ? "{$user->name} (ID: {$user->id})" : "System/Guest";
            $modelName = class_basename($model);
            
            // Format log message for traceability
            Log::info("[{$modelName}] Created: ID {$model->id} by {$userInfo}. Details: " . json_encode($model->getAttributes()));
        });

        static::updated(function ($model) {
            $user = auth()->user();
            $userInfo = $user ? "{$user->name} (ID: {$user->id})" : "System/Guest";
            $modelName = class_basename($model);
            
            // Check dirty fields to log differences
            $changes = [];
            foreach ($model->getDirty() as $key => $value) {
                $changes[$key] = [
                    'old' => $model->getOriginal($key),
                    'new' => $value
                ];
            }

            Log::info("[{$modelName}] Updated: ID {$model->id} by {$userInfo}. Changes: " . json_encode($changes));
        });

        static::deleted(function ($model) {
            $user = auth()->user();
            $userInfo = $user ? "{$user->name} (ID: {$user->id})" : "System/Guest";
            $modelName = class_basename($model);

            Log::info("[{$modelName}] Deleted: ID {$model->id} by {$userInfo}");
        });
    }
}
