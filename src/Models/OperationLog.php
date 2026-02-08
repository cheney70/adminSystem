<?php

namespace Cheney\AdminSystem\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OperationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'username',
        'module',
        'action',
        'method',
        'url',
        'ip',
        'user_agent',
        'params',
        'status',
        'error_message',
    ];

    protected $casts = [
        'status' => 'integer',
        'params' => 'array',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', 1);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 0);
    }

    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
