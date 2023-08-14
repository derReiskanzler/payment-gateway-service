<mj-section>
    <mj-column>
        <mj-text>
            Hello {{ $data->prospectFirstName()->toString() }},
        </mj-text>
        <mj-text>
            A new reservation has been requested for you.
        </mj-text>
    </mj-column>
</mj-section>

<mj-section>
    <mj-column>
        <mj-table>
            <tr>
                <th style="text-align:left; padding-bottom: 12px; font-weight: normal">Unit(s) requested for reservation:</th>
            </tr>
            @foreach($data->unitCollection()->units() as $unit)
            <tr>
                <th style="text-align:left; padding-bottom: 12px; font-weight: normal">{{ $unit->name()?->toString() }}</th>
                <td style="text-align:right; padding-bottom: 12px; white-space: nowrap;">{{ $unit->deposit()?->toFloat() }} €</td>
            </tr>
            @endforeach

            <tr style="border-top: 1px #1231 solid;">
                <th style="text-align:left; padding-bottom: 12px; padding-top: 12px; font-weight: normal">Total deposit:</th>
                <td style="text-align:right; padding-bottom: 12px; white-space: nowrap;">{{ $data->unitCollection()->totalUnitDeposit()->toFloat() }} €</td>
            </tr>
        </mj-table>
        <mj-button href="{!! $data->checkoutSessionUrl()->toString() !!}">Pay now</mj-button>


        <mj-text>
            The button will redirect you to our payment page, where you can pay the deposit for your property in order to get your reservation validated and ready for the next step!
        </mj-text>

        <mj-text>
            The payment is due at {{ $data->expiresAt()->format(\Allmyhomes\Domain\Context::EN_TIME_FORMAT) }} and will be cancelled automatically if the deposit is not paid.
        </mj-text>

        <mj-text>
            Your team from allmyhomes
        </mj-text>

    </mj-column>
</mj-section>

