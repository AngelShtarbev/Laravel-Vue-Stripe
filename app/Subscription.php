<?php

namespace App;

use Carbon\Carbon;
use Stripe\Customer;
use Stripe\Subscription as StripeSubscription;

class Subscription
{
    protected $user;

    protected $coupon;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function usingCoupon($coupon)
    {
        if($coupon) {
            $this->coupon = $coupon;
        }

        return $this;
    }

    public function create(Plan $plan, $token)
    {
        $customer = Customer::create([
            'email' => $this->user->email,
            'source' => $token,
            'plan' => $plan->name,
            'coupon' => $this->coupon
        ]);

        $subscriptionId = $customer->subscriptions->data[0]->id;

        $this->user->activate($customer->id, $subscriptionId);
    }

    public function cancel($atPeriodEnd = true)
    {
        $customer = $this->retrieveStripeCustomer();

        $subscription = $customer->cancelSubscription(['at_period_end' => $atPeriodEnd]);

        $endDate = Carbon::createFromTimestamp($subscription->current_period_end);

        $this->user->deactivate($endDate);
    }

    public function cancelImmediately()
    {
        return $this->cancel(false);
    }

    public function retrieveStripeCustomer()
    {
        return Customer::retrieve($this->user->stripe_id);
    }

    public function retrieveStripeSubscription()
    {
        return StripeSubscription::retrieve($this->user->stripe_subscription);
    }

    public function resume()
    {
        $subscription = $this->retrieveStripeCustomer();

        $subscription->plan = 'monthly';

        $subscription->save();

        $this->user->activate();
    }
}

