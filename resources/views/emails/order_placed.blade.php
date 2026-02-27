<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 80%; margin: 20px auto; border: 1px solid #ddd; padding: 20px; border-radius: 10px; }
        .header { background-color: #6A3DE8; color: white; padding: 10px; text-align: center; border-radius: 10px 10px 0 0; }
        .details { margin: 20px 0; }
        .footer { font-size: 12px; color: #777; text-align: center; margin-top: 20px; }
        .price { font-weight: bold; color: #6A3DE8; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ScentView Perfumes</h1>
        </div>
        <h2>Thank you for your order, {{ $order->user->name }}!</h2>
        <p>Your order has been successfully placed. Here are the details:</p>
        
        <div class="details">
            <p><strong>Order ID:</strong> #{{ $order->id }}</p>
            <p><strong>Total Amount:</strong> <span class="price">PKR {{ number_format($order->total_amount, 2) }}</span></p>
            <p><strong>Shipping Address:</strong> {{ $order->shipping_address }}</p>
            <p><strong>Phone Number:</strong> {{ $order->phone_number }}</p>
        </div>

        <p>We are processing your order and will notify you once it's shipped.</p>

        <div class="footer">
            <p>This is an automated email from ScentView. Please do not reply.</p>
            <p>© 2026 ScentView Perfumes, Nankana Sahib.</p>
        </div>
    </div>
</body>
</html>