<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Employee;
use App\Models\Image;
use App\Models\Korong;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\Request;
use Closure;

class BusinessService
{
    public static function createBusiness(
        float $longitude,
        float $latitude,
        string $name,
        string $address,
        string $phone,
        string $type,
        int $korongId,
        User $user,
        Closure $afterCreate
    ): ?Business {

        try {
            return DB::transaction(function () use (

                $longitude,
                $latitude,
                $name,
                $address,
                $phone,
                $type,
                $user,
                $korongId,
                $afterCreate

            ) {
                $korong = Korong::findOrFail($korongId);
                //dd($korong);
                if (! $korong) {
                    throw new Exception("Korong tidak ditemukan");
                }

                // 1. Create Business
                $business = Business::create([
                    'longitude' => $longitude,
                    'latitude' => $latitude,
                    'name' => $name,
                    'address' => $address,
                    'phone' => $phone,
                    'type' => $type,
                    'user_id' => $user->id,
                    'korong_id' => $korongId,
                ]);

                // 2. Create Owner Role
                $roleOwner = EmployeeRoleService::createEmployeeRole(
                    'Pemilik',
                    'Pemilik UMKM',
                    'Mengurus bisnis',
                    true,
                    $business->id
                );

                // 3. Add Owner as Employee
                $ownerEmployee = new Employee([
                    'user_id' => $user->id,
                    'role_id' => $roleOwner->id,
                    'start_date' => now(),
                    'salary' => 0.00,
                ]);
                if ($afterCreate instanceof Closure) {
                    $afterCreate($business);
                }
                EmployeeService::createEmployeeFromAdmin($user, $roleOwner, $ownerEmployee);

                return $business;
            });
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
        return null;
    }
}
