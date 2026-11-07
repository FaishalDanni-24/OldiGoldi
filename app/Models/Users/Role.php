<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['role_name'])]
class Role extends Model
{
    protected $table = 'roles';

    protected $primaryKey = 'role_id';

    public $timestamps = false;

    /** @use HasFactory<\Database\Factories\RoleFactory> */
    use HasFactory;

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id', 'role_id');
    }
}
