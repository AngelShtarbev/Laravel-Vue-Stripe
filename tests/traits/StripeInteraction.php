<?php

use App\Plan;
use Stripe\Token;

trait StripeInteraction
{
    protected function getStripeToken()
    {
       return Token::create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 1,
                'exp_year' => 2019,
                'cvc' => 508
            ]
        ])->id;
    }

    protected function getPlan()
    {
       return new Plan(['name' => 'monthly']);
    }
}