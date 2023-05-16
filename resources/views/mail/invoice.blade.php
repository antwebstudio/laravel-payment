<div class="table">
<table>
<thead>
<tr>
<th style="text-align: left">Item</th>
<th style="text-align: left">Price</th>
<th>Quantity</th>
<th>Subtotal</th>
</tr>
</thead>
<tbody>
@foreach($invoice->items as $item)
<tr>
<td>{{ $item->name }}</td>
<td>{{ $item->unit_price }}</td>
<td style="text-align: center">{{ $item->quantity }}</td>
<td style="text-align: right">{{ $item->display_total }}</td>
</tr>
@endforeach
{{-- <tr>
<td colspan="3" style="text-align: right">
Subtotal:
</td>
<td style="text-align: right">
{{ $order->displaySubtotal() }}
</td>
</tr> --}}
{{-- @foreach($invoice->getCharges() as $charges)
@if($charges->displayPrice() != null)
<tr>
<td colspan="3" style="text-align: right">
    {{ $charges->getText() }}
</td>
<td style="text-align: right">
    {{ $charges->displayPrice(false) }}
</td>
</tr>
@endif
@endforeach --}}
</tbody>
<tfoot>
<tr>
<td colspan="3" style="text-align: right">
<b>Total:</b>
</td>
<td style="text-align: right">
{{ $invoice->display_total }}
</td>
</tr>
</tfoot>
</table>
</div>