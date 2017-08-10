<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
    <script>
        var Laravel = {
            stripeKey: "{{ config('services.stripe.key') }}"
        }
    </script>
    </head>
    <body>
    <div id="app">
       <checkout-form :plans="{{$plans}}"></checkout-form>
    </div>
    <script type="text/javascript" src="https://checkout.stripe.com/checkout.js"></script>
    <script src="/js/app.js"></script>
    </body>
</html>
