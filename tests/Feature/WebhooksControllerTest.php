<?php

namespace Tests\Feature;

use App\Http\Controllers\WebhooksController;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WebhooksControllerTest extends TestCase
{

   use DatabaseTransactions;

   /** @test */
   public function convert_stripe_event_name_to_method_name()
   {
       $name = (new WebhooksController)->eventToMethod('customer.subscription.deleted');

       $this->assertEquals('whenCustomerSubscriptionDeleted', $name);
   }

   /** @test */
   public function deactivate_users_subscription_if_deleted_on_stripes_end()
   {
       $user = factory('App\User')->create([
           'stripe_active' => 1,
           'stripe_id' => 'fake_stripe_id'
       ]);

       $this->post('stripe/webhook', [
           'type' => 'customer.subscription.deleted',
           'data' => [
               'object' => [
                    'customer' => $user->stripe_id
               ]
           ]
       ]);

       $user = $user->fresh();

       $this->assertFalse($user->fresh()->isSubscribed());
   }
}
