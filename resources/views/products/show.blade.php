@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Some Product</h1>
        @if(!auth()->check())
           <p>Please <a href="/login">sign in</a> to test the product.</p>
        @elseif(!auth()->user()->isActive())
            <p>Please <a href="/home">reactivate your account to view this lesson.</a></p>
        @else
            <p>Account is active</p>
        @endif
    </div>
@endsection