<mj-section>
    <mj-column>
        <mj-text>
            Hallo {{ $data->prospectFirstName()->toString() }},
        </mj-text>
        <mj-text>
            Eine neue Reservierung wurde für Sie angefragt.
        </mj-text>
    </mj-column>
</mj-section>

<mj-section>
    <mj-column>
        <mj-table>
            <tr>
                <th style="text-align:left; padding-bottom: 12px; font-weight: normal">Folgende Wohneinheit(en) wurden für die Reservierung angefragt:</th>
            </tr>
            @foreach($data->unitCollection()->units() as $unit)
            <tr>
                <th style="text-align:left; padding-bottom: 12px; font-weight: normal">{{ $unit->name()?->toString() }}</th>
                <td style="text-align:right; padding-bottom: 12px; white-space: nowrap;">{{ $unit->deposit()?->toFloat() }} €</td>
            </tr>
            @endforeach

            <tr style="border-top: 1px #1231 solid;">
                <th style="text-align:left; padding-bottom: 12px; padding-top: 12px; font-weight: normal">Gesamte Reservierungsgebühr:</th>
                <td style="text-align:right; padding-bottom: 12px; white-space: nowrap;">{{ $data->unitCollection()->totalUnitDeposit()->toFloat() }} €</td>
            </tr>
        </mj-table>
        <mj-button href="{!! $data->checkoutSessionUrl()->toString() !!}">Jetzt Bezahlen</mj-button>


        <mj-text>
            Der Link wird Sie zu unserer Bezahlseite weiterleiten, wo Sie die Reservierungsgebühr für die Immobilie(n) bezahlen können, damit Ihre Reservierung als gültig gilt und für die nächsten Schritte bereit steht!
        </mj-text>

        <mj-text>
            Die Zahlung ist bis zum {{ $data->expiresAt()->format(\Allmyhomes\Domain\Context::DE_TIME_FORMAT )}} fällig und wird automatisch storniert, falls Sie die Reservierungsgebühr nicht zum Fälligkeitsdatum bezahlen.
        </mj-text>

        <mj-text>
            Dein Team von allmyhomes
        </mj-text>

    </mj-column>
</mj-section>

