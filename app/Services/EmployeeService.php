<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeRole;
use App\Models\User;

class EmployeeService
{
    public static function createEmployee(User $user, EmployeeRole $role, Employee $employee): Employee
    {
        $employee->user()->associate($user);
        $employee->role()->associate($role);
        $employee->save();

        return $employee;
    }

    public static function createEmployeeBusinessOwner(User $user, EmployeeRole $role, Employee $employee): Employee
    {
        if ($role->has_admin) {
            throw new \Exception("You do not have permission.");
        }

        return self::createEmployee($user, $role, $employee);
    }

    public static function createEmployeeFromAdmin(User $user, EmployeeRole $role, Employee $employee): Employee
    {
        return self::createEmployee($user, $role, $employee);
    }
}
