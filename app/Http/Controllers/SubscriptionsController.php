<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrationForm;
use League\Flysystem\Exception;

class SubscriptionsController extends Controller
{
    public function store(RegistrationForm $form)
    {
       try {
           $form->save();
       } catch (Exception $e) {
           return response()->json(
              ['status' => $e->getMessage()], 422
           );
       }

        return [
           'status' => 'Payment Successful !'
        ];
    }

    public function destroy()
    {
      auth()->user()->subscription()->cancel();

      return back();
    }

    public function update()
    {
       if(request('resume')) {
           auth()->user()->subscription()->resume();
       }

       return back();
    }
}
