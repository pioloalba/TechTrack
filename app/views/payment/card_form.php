<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Payment - TechTrack</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .payment-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
            padding: 40px;
        }
        
        .payment-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .payment-header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .amount-display {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .amount-display .label {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .amount-display .amount {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .card-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .pay-button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
            margin-top: 20px;
        }
        
        .pay-button:hover {
            transform: translateY(-2px);
        }
        
        .pay-button:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        
        .security-info {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }
        
        .security-info svg {
            width: 20px;
            height: 20px;
            vertical-align: middle;
            margin-right: 5px;
        }
        
        .test-card-info {
            background: #fff3cd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #ffc107;
        }
        
        .test-card-info h4 {
            color: #856404;
            margin-bottom: 10px;
        }
        
        .test-card-info p {
            color: #856404;
            font-size: 14px;
            margin: 5px 0;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
        }
        
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="payment-header">
            <h1>💳 Card Payment</h1>
            <p>Order #<?= $order['id'] ?></p>
        </div>
        
        <div class="amount-display">
            <div class="label">Total Amount</div>
            <div class="amount">₱<?= number_format($amount, 2) ?></div>
        </div>
        
        <div class="test-card-info">
            <h4>🧪 Test Mode</h4>
            <p><strong>Test Card:</strong> 4343 4343 4343 4345</p>
            <p><strong>Expiry:</strong> Any future date (e.g., 12/25)</p>
            <p><strong>CVC:</strong> Any 3 digits (e.g., 123)</p>
        </div>
        
        <div class="error-message" id="errorMessage"></div>
        
        <form id="paymentForm">
            <div class="form-group">
                <label for="cardNumber">Card Number</label>
                <input type="text" 
                       id="cardNumber" 
                       placeholder="4343 4343 4343 4345" 
                       maxlength="19" 
                       required>
            </div>
            
            <div class="card-row">
                <div class="form-group">
                    <label for="expiry">Expiry (MM/YY)</label>
                    <input type="text" 
                           id="expiry" 
                           placeholder="12/25" 
                           maxlength="5" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="cvc">CVC</label>
                    <input type="text" 
                           id="cvc" 
                           placeholder="123" 
                           maxlength="4" 
                           required>
                </div>
            </div>
            
            <button type="submit" class="pay-button" id="payButton">
                Pay ₱<?= number_format($amount, 2) ?>
            </button>
        </form>
        
        <div class="security-info">
            🔒 Secured by PayMongo - Your payment is safe and encrypted
        </div>
    </div>

    <script>
        const clientKey = '<?= $client_key ?>';
        const publicKey = '<?= $public_key ?>';
        const paymentIntentId = '<?= $payment_intent_id ?>';
        
        // Format card number with spaces
        document.getElementById('cardNumber').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            let formatted = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formatted;
        });
        
        // Format expiry
        document.getElementById('expiry').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });
        
        // Only allow numbers in CVC
        document.getElementById('cvc').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });
        
        // Handle form submission
        document.getElementById('paymentForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const payButton = document.getElementById('payButton');
            const errorMessage = document.getElementById('errorMessage');
            
            payButton.disabled = true;
            payButton.textContent = 'Processing...';
            errorMessage.style.display = 'none';
            
            try {
                // Get card details
                const cardNumber = document.getElementById('cardNumber').value.replace(/\s/g, '');
                const expiry = document.getElementById('expiry').value.split('/');
                const cvc = document.getElementById('cvc').value;
                
                // Create payment method
                const pmResponse = await createPaymentMethod(cardNumber, expiry[0], expiry[1], cvc);
                
                if (!pmResponse.data) {
                    throw new Error('Failed to create payment method');
                }
                
                // Attach to payment intent
                const attachResponse = await attachPaymentIntent(pmResponse.data.id);
                
                if (attachResponse.data.attributes.status === 'awaiting_next_action') {
                    // Redirect to 3D Secure
                    window.location.href = attachResponse.data.attributes.next_action.redirect.url;
                } else if (attachResponse.data.attributes.status === 'succeeded') {
                    // Payment successful
                    window.location.href = '<?= site_url("payment/success?order_id=" . $order["id"]) ?>';
                } else {
                    throw new Error('Payment failed');
                }
                
            } catch (error) {
                errorMessage.textContent = error.message || 'Payment failed. Please try again.';
                errorMessage.style.display = 'block';
                payButton.disabled = false;
                payButton.textContent = 'Pay ₱<?= number_format($amount, 2) ?>';
            }
        });
        
        async function createPaymentMethod(cardNumber, expMonth, expYear, cvc) {
            const response = await fetch('https://api.paymongo.com/v1/payment_methods', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Basic ' + btoa(publicKey + ':')
                },
                body: JSON.stringify({
                    data: {
                        attributes: {
                            type: 'card',
                            details: {
                                card_number: cardNumber,
                                exp_month: parseInt(expMonth),
                                exp_year: parseInt('20' + expYear),
                                cvc: cvc
                            }
                        }
                    }
                })
            });
            
            const result = await response.json();
            
            if (!response.ok) {
                console.error('Payment method error:', result);
                throw new Error(result.errors?.[0]?.detail || 'Invalid card details');
            }
            
            return result;
        }
        
        async function attachPaymentIntent(paymentMethodId) {
            const response = await fetch(`https://api.paymongo.com/v1/payment_intents/${paymentIntentId}/attach`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Basic ' + btoa(publicKey + ':')
                },
                body: JSON.stringify({
                    data: {
                        attributes: {
                            payment_method: paymentMethodId,
                            client_key: clientKey,
                            return_url: '<?= site_url("payment/success?order_id=" . $order["id"]) ?>'
                        }
                    }
                })
            });
            
            const result = await response.json();
            
            if (!response.ok) {
                console.error('Attach error:', result);
                throw new Error(result.errors?.[0]?.detail || 'Failed to process payment');
            }
            
            return result;
        }
    </script>
</body>
</html>
