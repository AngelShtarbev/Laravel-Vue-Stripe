<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class WebhooksController extends Controller
{
    public function handle()
    {
        $payload = request()->all();

        $method = $this->eventToMethod($payload['type']);

        if(method_exists($this, $method)) {
            $this->$method($payload);
        }

        return response('Webhook Received');
    }

    public function whenChargeSucceeded($payload)
    {
      $this->retrieveUser($payload)->payments()->create([
          'amount' => $payload['data']['object']['amount'],
          'charge_id' => $payload['data']['object']['id']
      ]);
    }

    public function whenCustomerSubscriptionDeleted($payload)
    {
        $this->retrieveUser($payload)->deactivate();
    }

    public function eventToMethod($event)
    {
        return 'when' . studly_case(str_replace('.','_', $event));
    }

    protected function retrieveUser($payload)
    {
        return User::byStripeId($payload['data']['object']['customer']);
    }
}
