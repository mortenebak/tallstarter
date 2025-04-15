<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Laravel\Cashier\Subscription;

class SubscriptionPolicy
{
    use HandlesAuthorization;

    public function cancel(User $user, Subscription $subscription)
    {
        return ! $subscription->canceled();
    }

    public function resume(User $user, Subscription $subscription)
    {
        return $subscription->canceled();
    }
}
