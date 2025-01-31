<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stripe Payment</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <h1>Stripe Payment Test</h1>
    
    <form id="payment-form">
        <div id="card-element"></div>
        <button id="submit">Pay</button>
    </form>

    <script>
        // Stripeの公開キーを設定
        console.log('{{ env('STRIPE_KEY') }}');
        var stripe = Stripe('{{ env('STRIPE_KEY') }}');
        var elements = stripe.elements();

        // Card Elementの作成
        var card = elements.create('card');
        card.mount('#card-element');

        // フォーム送信時の処理
        var form = document.getElementById('payment-form');
        form.addEventListener('submit', async function(event) {
            event.preventDefault();

            // サーバーでPaymentIntentを作成
            const response = await fetch('/process-payment', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ amount: 5000 })
            });
            const { clientSecret } = await response.json();

            if (error) {
                console.log('Error creating PaymentIntent: ' + error);
                alert('Error: ' + error);
                return;
            }
    
            const { paymentIntent, error } = await stripe.confirmCardPayment(clientSecret, {
                payment_method: {
                    card: card,
                }
            });

            if (stripeError) {
                console.log('Stripe error: ' + stripeError.message);
            } else if (paymentIntent.status === 'succeeded') {
                alert('Payment Successful!');
            }
        });
    </script>
</body>
</html>
