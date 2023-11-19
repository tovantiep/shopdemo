<!DOCTYPE html>
<html>
<head>
    <title>Đơn hàng đã được xác nhận</title>
</head>
<body>

<p>Xin chào {{ $data['user_name']}},</p>

<p>Đơn hàng của bạn đã được xác nhận. Chi tiết đơn hàng:</p>

<ul>
    @foreach($data['orderItems'] as $orderItem)
        <li>{{ $orderItem->product->name }} - {{ $orderItem->quantity }} sản phẩm</li>
    @endforeach
</ul>

<p>Tổng số tiền: {{ $data['totalAmount'] }}</p>

<p>Cảm ơn bạn đã đặt hàng!</p>
</body>
</html>


