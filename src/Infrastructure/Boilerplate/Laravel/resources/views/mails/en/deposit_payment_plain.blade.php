Hello {{ $data->prospectFirstName()->toString() }},

A new reservation has been requested for you.

Unit(s) requested for reservation:

@foreach($data->unitCollection()->units() as $unit)
- {{ $unit->name()?->toString() }}, {{ $unit->deposit()?->toFloat() }} €
@endforeach

Total deposit: {{ $data->unitCollection()->totalUnitDeposit()->toFloat() }} €

Pay now: {{ $data->checkoutSessionUrl()->toString() }}

The link will redirect you to our payment page, where you can pay the deposit for your property in order to get your reservation validated and ready for the next step!

The payment is due at {{ $data->expiresAt()->format(\Allmyhomes\Domain\Context::EN_TIME_FORMAT) }} and will be cancelled automatically if the deposit is not paid.

Your team from allmyhomes
