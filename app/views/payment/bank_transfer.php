<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Transfer - TechTrack</title>
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
            max-width: 600px;
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
        
        .bank-info {
            background: #fff;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
        }
        
        .bank-info h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 18px;
        }
        
        .bank-detail {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .bank-detail:last-child {
            border-bottom: none;
        }
        
        .bank-detail .label {
            color: #666;
            font-size: 14px;
        }
        
        .bank-detail .value {
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        
        .copy-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 12px;
            cursor: pointer;
            margin-left: 10px;
        }
        
        .copy-btn:hover {
            background: #5568d3;
        }
        
        .instructions {
            background: #fff3cd;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #ffc107;
            margin-bottom: 20px;
        }
        
        .instructions h4 {
            color: #856404;
            margin-bottom: 15px;
        }
        
        .instructions ol {
            margin-left: 20px;
            color: #856404;
        }
        
        .instructions li {
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .upload-section {
            margin-bottom: 20px;
        }
        
        .upload-section h4 {
            color: #333;
            margin-bottom: 15px;
        }
        
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }
        
        .file-input-wrapper input[type=file] {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }
        
        .file-input-label {
            display: block;
            padding: 15px;
            background: #f8f9fa;
            border: 2px dashed #d0d0d0;
            border-radius: 8px;
            text-align: center;
            color: #666;
            cursor: pointer;
        }
        
        .file-input-label:hover {
            background: #e9ecef;
            border-color: #667eea;
        }
        
        .reference-input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            margin-top: 15px;
        }
        
        .reference-input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .submit-button {
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
        
        .submit-button:hover {
            transform: translateY(-2px);
        }
        
        .security-info {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="payment-header">
            <h1>🏦 Bank Transfer Payment</h1>
            <p>Order #<?= $order['id'] ?></p>
        </div>
        
        <div class="amount-display">
            <div class="label">Total Amount to Pay</div>
            <div class="amount">₱<?= number_format($amount, 2) ?></div>
        </div>
        
        <div class="bank-info">
            <h3>Transfer to this account:</h3>
            
            <div class="bank-detail">
                <span class="label">Bank Name:</span>
                <span class="value">
                    BDO (Banco de Oro)
                    <button class="copy-btn" onclick="copyText('BDO')">Copy</button>
                </span>
            </div>
            
            <div class="bank-detail">
                <span class="label">Account Name:</span>
                <span class="value">
                    TechTrack Store
                    <button class="copy-btn" onclick="copyText('TechTrack Store')">Copy</button>
                </span>
            </div>
            
            <div class="bank-detail">
                <span class="label">Account Number:</span>
                <span class="value">
                    1234-5678-9012
                    <button class="copy-btn" onclick="copyText('123456789012')">Copy</button>
                </span>
            </div>
            
            <div class="bank-detail">
                <span class="label">Reference Number:</span>
                <span class="value">
                    ORDER-<?= $order['id'] ?>
                    <button class="copy-btn" onclick="copyText('ORDER-<?= $order['id'] ?>')">Copy</button>
                </span>
            </div>
        </div>
        
        <div class="instructions">
            <h4>📋 Payment Instructions:</h4>
            <ol>
                <li>Transfer the exact amount shown above to the bank account</li>
                <li>Use the reference number <strong>ORDER-<?= $order['id'] ?></strong> when transferring</li>
                <li>Take a screenshot or photo of the transfer confirmation</li>
                <li>Upload the proof of payment below</li>
                <li>We'll verify your payment within 24 hours</li>
            </ol>
        </div>
        
        <form action="<?= site_url('payment/confirm-bank-transfer') ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
            
            <div class="upload-section">
                <h4>Upload Proof of Payment:</h4>
                <div class="file-input-wrapper">
                    <input type="file" name="proof_of_payment" id="proofFile" accept="image/*" required>
                    <label for="proofFile" class="file-input-label" id="fileLabel">
                        📎 Click here to upload screenshot/photo
                    </label>
                </div>
                
                <input type="text" 
                       name="reference_number" 
                       placeholder="Enter Bank Reference Number (Optional)" 
                       class="reference-input">
            </div>
            
            <button type="submit" class="submit-button">
                Submit Payment Proof
            </button>
        </form>
        
        <div class="security-info">
            🔒 Your payment information is secure
        </div>
    </div>

    <script>
        function copyText(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Copied: ' + text);
            });
        }
        
        document.getElementById('proofFile').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'Click here to upload screenshot/photo';
            document.getElementById('fileLabel').textContent = fileName;
        });
    </script>
</body>
</html>
