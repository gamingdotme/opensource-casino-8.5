@component('mail::message')
# Hello Admin, 

There is new withdrawal request from user {{$details['username']}}.<br>
below is more details about withdrawal request.

Amount: {{$details['amount']}}<br>
Currency: {{$details['currency']}}

Thank you!!<br>
{{ config('app.name') }}
@endcomponent
