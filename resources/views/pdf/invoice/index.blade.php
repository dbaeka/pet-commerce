<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #{{$order->uuid}}</title>
    @include('pdf.invoice.style')
</head>

<body>
<div class="invoice-box">
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="2">
                <table>
                    <tr>
                        <td class="title">{{config('app.name')}}</td>
                        <td>
                            <strong>Date:</strong> {{$order->created_at->format('d-m-Y')}}
                            <br/>
                            <strong>Invoice #:</strong> {{$order->uuid}}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr class="information">
            <td colspan="2">
                <table>
                    <tr>
                        <td width="50%">
                            <strong>Customer Details</strong><br/>
                            First Name: {{$order->user->first_name}}<br/>
                            Last Name: {{$order->user->last_name}}<br/>
                            ID: {{$order->user->uuid}}<br/>
                            Phone Number: {{$order->user->phone_number}}<br/>
                            Email: {{$order->user->email}}<br/>
                            Address: {{$order->user->address}}
                        </td>

                        <td>
                            <strong>Billing/Shipping Details</strong><br/>
                            Billing: {{$order->address->billing}}<br/>
                            Shipping: {{$order->address->shipping}}<br/><br/>
                            <strong>Payment Details</strong><br/>
                            Payment Type: {{strtoupper($order->payment->type->value)}}<br/>
                            @if($order->payment->type->value !== "credit_card")
                                @foreach ($order->payment->details as $key => $value)
                                    {{ Str::title($key) }}: {{ is_bool($value) ? ($value ? 'YES' : 'NO') : $value }}
                                    <br/>
                                @endforeach
                            @else
                                Holder Name: {{ $order->payment->details->holder_name }}<br/>
                                Number: {{Str::mask($order->payment->details->number,'*',0,-4)}}
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <h5>Items</h5>
    <table cellpadding="0" cellspacing="0">
        <tr class="heading">
            <td class="text-center">#</td>
            <td>ID</td>
            <td width="30%">Item Name</td>
            <td width="8%">Quantity</td>
            <td width="8%">Unit Price</td>
            <td width="8%">Price</td>
        </tr>

        @foreach($order->products as $product)
            <tr class="item">
                <td class="text-center">{{$loop->iteration}}</td>
                <td class="text-small">{{$product->uuid}}</td>
                <td>{{$product->product}}</td>
                <td class="text-center">{{$product->quantity}}</td>
                <td class="text-right no-wrap">$ {{$product->price}}</td>
                <td class="text-right no-wrap">$ {{round($product->price * $product->quantity,2)}}</td>
            </tr>
        @endforeach
    </table>
    <br/>
    <hr/>
    <h5 class="text-right">Total</h5>
    <table class="avoid-table-break" style="width: 50%;  margin-right: 0px; margin-left: auto; border: 5px #eee solid;">
        <tr>
            <td width="80%" style=""><strong>Subtotal</strong></td>
            <td class="text-right no-wrap">$ {{$order->amount}}</td>
        </tr>
        <tr>
            <td><strong>Delivery fee</strong></td>
            <td class="text-right no-wrap">$ {{$order->delivery_fee}}</td>
        </tr>
        <tr>
            <td><strong>TOTAL</strong></td>
            <td class="text-right no-wrap"><strong>$ {{$order->amount + $order->delivery_fee}}</strong></td>
        </tr>
    </table>
</div>
</body>
</html>
