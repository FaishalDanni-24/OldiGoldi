<?php

namespace Database\Factories\Users;

use App\Models\Users\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'role_name' => 'role_' . fake()->unique()->numerify('######'),
        ];
    }
}
