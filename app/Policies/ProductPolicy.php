<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use App\Models\Business;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;
    public static function permittedModify(User $user, Product $product): bool
    {
        return $user->id === $product->business->user_id || $user->hasRole('admin');
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Product $product): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Business $business): bool
    {
        // User must be the owner of THIS specific business
        return $user->id === $business->user_id;
    }


    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $product): bool
    {
        return ProductPolicy::permittedModify($user, $product);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Product $product): bool
    {
        return ProductPolicy::permittedModify($user, $product);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Product $product): bool
    {
        return ProductPolicy::permittedModify($user, $product);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return ProductPolicy::permittedModify($user, $product);
    }
}
