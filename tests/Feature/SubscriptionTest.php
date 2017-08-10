<?php

namespace Tests\Feature;

use App\Subscription;
use League\Flysystem\Exception;
use Stripe\Subscription as StripeSubscription;
use StripeInteraction;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SubscriptionTest extends TestCase
{
    use DatabaseTransactions;
    use StripeInteraction;

    /** @test */
    public function subscribes_user()
    {
       $user = $this->makeSubscribedUser(['stripe_active' => false]);

       $user = $user->fresh();

       $this->assertTrue($user->isSubscribed());

       try {
           $user->subscription()->retrieveStripeSubscription();
       } catch (Exception $e) {
           $this->fail('adaw');
       }
    }

    /** @test */
    public function subscribes_user_using_coupon_code()
    {
        $user = factory('App\User')->create();

        $user->subscription()->usingCoupon('TEST-COUPON')->create($this->getPlan(), $this->getStripeToken());

        $customer = $user->subscription()->retrieveStripeCustomer();

        try {
            $couponThatWasAppliedToStripe = $customer->invoices()->data[0]->discount->coupon->id;

            $this->assertEquals('TEST-COUPON', $couponThatWasAppliedToStripe);
        } catch (Exception $e) {
            $this->fail('Expected a coupon to be applied to the Stripe customer, but did not find one.');
        }

    }

    /** @test */
    public function cancel_user_subscription()
    {
       $user = $this->makeSubscribedUser();

       $user->subscription()->cancel();

       $stripeSubscription = $user->subscription()->retrieveStripeSubscription();

       $this->assertNotNull($stripeSubscription->canceled_at);

       $this->assertFalse($user->isSubscribed());

       $this->assertNotNull($user->subscription_end_at);
    }

    /** @test */
    public function makeSubscribedUser($overrides = [])
    {
        $user = factory('App\User')->create($overrides);

        $user->subscription()->create($this->getPlan(), $this->getStripeToken());

        return $user;
    }
}
