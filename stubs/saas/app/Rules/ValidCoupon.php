<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Laravel\Cashier\Cashier;
use Stripe\Coupon as StripeCoupon;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\InvalidRequestException;

class ValidCoupon implements Rule
{
    protected $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     *
     * @throws ApiErrorException
     */
    public function passes($attribute, $value): bool
    {
        try {
            $coupon = StripeCoupon::retrieve($value, Cashier::stripeOptions());

            if (! $coupon->valid) {
                $this->message = 'Coupon is invalid!';

                return false;
            }
        } catch (InvalidRequestException $e) {
            $this->message = 'The coupon does not exists.';

            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return $this->message;
    }
}
