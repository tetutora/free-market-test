<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use App\Models\User;
use Mockery;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;

class PurchaseProductTest extends TestCase
{
    use RefreshDatabase;

    
}
