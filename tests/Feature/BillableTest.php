<?php

namespace Tests\Feature;

use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BillableTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function determine_if_users_subscriptions_are_active()
    {
        $user = factory('App\User')->create([
            'stripe_active' => true,
            'subscription_end_at' => null
        ]);

        $this->assertTrue($user->isActive());

        $user->update([
            'stripe_active' => false,
            'subscription_end_at' => Carbon::now()->addDays(2)
        ]);

        $this->assertTrue($user->isActive());

        $user->update([
            'stripe_active' => false,
            'subscription_end_at' => Carbon::now()->subDays(2)
        ]);

        $this->assertFalse($user->isActive());
    }

    /** @test */
    public function determine_if_users_subscription_is_on_grace_period()
    {
        $user = factory('App\User')->create([
            'subscription_end_at' => null
        ]);

        $this->assertFalse($user->isOnGracePeriod());

        $user->subscription_end_at = Carbon::now()->addDays(2);

        $this->assertTrue($user->isOnGracePeriod());

        $user->subscription_end_at = Carbon::now()->subDays(2);

        $this->assertFalse($user->isOnGracePeriod());
    }
}
