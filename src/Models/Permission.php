<?php

namespace Cheney\AdminSystem\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'menu_id',
        'type',
    ];

    protected $casts = [
        'type' => 'integer',
        'menu_id' => 'integer',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_role');
    }
}
