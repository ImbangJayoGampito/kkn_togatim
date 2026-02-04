<?php

namespace App\Services;

use App\Models\EmployeeRole;

class EmployeeRoleService
{
    /**
     * Create a new employee role.
     *
     * @param  string  $name
     * @param  string  $description
     * @param  string  $permissions
     * @param  bool    $hasAdmin
     * @param  int     $businessId
     * @return EmployeeRole
     */
    public static function createEmployeeRole(
        string $name,
        string $description,
        string $permissions,
        bool $hasAdmin,
        int $businessId
    ): EmployeeRole {
        return EmployeeRole::create([
            'name' => $name,
            'description' => $description,
            'permissions' => $permissions,
            'has_admin' => $hasAdmin,
            'business_id' => $businessId,
        ]);
    }
}
