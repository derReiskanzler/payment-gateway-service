Hallo {{ $data->prospectFirstName()->toString() }},

Eine neue Reservierung wurde für Sie angefragt.

Folgende Wohneinheit(en) wurden für die Reservierung angefragt:

@foreach($data->unitCollection()->units() as $unit)
- {{ $unit->name()?->toString() }}, {{ $unit->deposit()?->toFloat() }} €
@endforeach

Gesamte Reservierungsgebühr: {{ $data->unitCollection()->totalUnitDeposit()->toFloat() }} €

Jetzt Bezahlen: {{ $data->checkoutSessionUrl()->toString() }}

Der Link wird Sie zu unserer Bezahlseite weiterleiten, wo Sie die Reservierungsgebühr für die Immobilie(n) bezahlen können, damit Ihre Reservierung als gültig gilt und für die nächsten Schritte bereit steht!

Die Zahlung ist bis zum {{ $data->expiresAt()->format(\Allmyhomes\Domain\Context::DE_TIME_FORMAT) }} fällig und wird automatisch storniert, falls Sie die Reservierungsgebühr nicht zum Fälligkeitsdatum bezahlen.

Dein Team von allmyhomes
