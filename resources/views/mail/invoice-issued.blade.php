@section('invoice-header')
Registration No. {{ config('invoice.registration_number') }} \
Address : {{ config('invoice.address') }} \
Email : {{ config('invoice.email') }} \
@endsection

@component('mail::message')

@if($invoice->isPaid())

Thank you for your payment.

<h2>RECEIPT</h2>

@yield('invoice-header')

Date: {{ $invoice->display_date }}
{{-- Invoice: {{ $invoice->reference }} --}}

Attn: \
{{ $invoice->display_attendee }}
{{-- Membership Expire Date: 2022-03-30 23:59:59 --}}
{{-- IC: 310674956 --}}

@include('payment::mail.invoice', ['invoice' => $invoice])

{{-- @component('mail::table')
| **Name**                                       | **Email**             | **Telephone**           |
| ---------------------------------------------- |:---------------------:| -----------------------:|
| {{$enquiry->firstname}} {{$enquiry->lastname}} | <{{$enquiry->email}}> | {{$enquiry->telephone}} |
@endcomponent --}}

@else

This is a notice that an invoice has been generated on {{ $invoice->created_at->toDateString() }}. 

<h2>INVOICE</h2>

@yield('invoice-header')

@include('payment::mail.invoice', ['invoice' => $invoice])

@component('mail::button', ['url' => url($invoice->paymentUrl)])
    Make Payment
@endcomponent

@endif

Thanks you!

Regards, \
{{ config('app.name') }}

@endcomponent

{{-- Footer --}}
{{-- @slot ('footer')
@component('mail::footer')
    If you’re having trouble clicking the "View Order" button, copy and paste the URL below into your web browser: {{ url($invoice->paymentUrl) }}
@endcomponent
@endslot --}}