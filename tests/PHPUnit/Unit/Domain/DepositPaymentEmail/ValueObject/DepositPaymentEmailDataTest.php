<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentEmail\ValueObject;

use Allmyhomes\Domain\DepositPaymentEmail\ValueObject\DepositPaymentEmailData;
use Generator;
use PHPUnit\Framework\TestCase;

final class DepositPaymentEmailDataTest extends TestCase
{
    /**
     * @param array<string, mixed> $depositPaymentEmailDataArray
     * @dataProvider provideDepositPaymentEmailData
     */
    public function testFromArray(array $depositPaymentEmailDataArray): void
    {
        $depositPaymentEmailData = DepositPaymentEmailData::fromArray($depositPaymentEmailDataArray);

        $this->assertInstanceOf(
            DepositPaymentEmailData::class,
            $depositPaymentEmailData,
            'created deposit payment email from array does not match expected class: DepositPaymentEmailData.'
        );
    }

    /**
     * @param array<string, mixed> $depositPaymentEmailDataArray
     * @dataProvider provideDepositPaymentEmailData
     */
    public function testReservationId(array $depositPaymentEmailDataArray): void
    {
        $depositPaymentEmailData = DepositPaymentEmailData::fromArray($depositPaymentEmailDataArray);

        $this->assertEquals(
            $depositPaymentEmailDataArray[DepositPaymentEmailData::RESERVATION_ID],
            $depositPaymentEmailData->reservationId()->toString(),
            'reservation id of deposit payment email from array does not match expected string.'
        );
    }

    /**
     * @param array<string, mixed> $depositPaymentEmailDataArray
     * @dataProvider provideDepositPaymentEmailData
     */
    public function testLanguage(array $depositPaymentEmailDataArray): void
    {
        $depositPaymentEmailData = DepositPaymentEmailData::fromArray($depositPaymentEmailDataArray);

        self::assertEquals(
            $depositPaymentEmailDataArray[DepositPaymentEmailData::LANGUAGE],
            $depositPaymentEmailData->language()->toString(),
            'language of deposit payment email does not match expected string.'
        );
    }

    /**
     * @param array<string, mixed> $depositPaymentEmailDataArray
     * @dataProvider provideDepositPaymentEmailData
     */
    public function testProspectId(array $depositPaymentEmailDataArray): void
    {
        $depositPaymentEmailData = DepositPaymentEmailData::fromArray($depositPaymentEmailDataArray);

        self::assertEquals(
            $depositPaymentEmailDataArray[DepositPaymentEmailData::PROSPECT_ID],
            $depositPaymentEmailData->prospectId()->toString(),
            'prospect id of deposit payment email does not match expected string.'
        );
    }

    /**
     * @param array<string, mixed> $depositPaymentEmailDataArray
     * @dataProvider provideDepositPaymentEmailData
     */
    public function testProspectEmail(array $depositPaymentEmailDataArray): void
    {
        $depositPaymentEmailData = DepositPaymentEmailData::fromArray($depositPaymentEmailDataArray);

        self::assertEquals(
            $depositPaymentEmailDataArray[DepositPaymentEmailData::PROSPECT_EMAIL],
            $depositPaymentEmailData->prospectEmail()->toString(),
            'prospect email of deposit payment email does not match expected string.'
        );
    }

    /**
     * @param array<string, mixed> $depositPaymentEmailDataArray
     * @dataProvider provideDepositPaymentEmailData
     */
    public function testProspectFirstName(array $depositPaymentEmailDataArray): void
    {
        $depositPaymentEmailData = DepositPaymentEmailData::fromArray($depositPaymentEmailDataArray);

        self::assertEquals(
            $depositPaymentEmailDataArray[DepositPaymentEmailData::PROSPECT_FIRST_NAME],
            $depositPaymentEmailData->prospectFirstName()?->toString(),
            'prospect first name of deposit payment email does not match expected string.'
        );
    }

    /**
     * @param array<string, mixed> $depositPaymentEmailDataArray
     * @dataProvider provideDepositPaymentEmailData
     */
    public function testProspectLastName(array $depositPaymentEmailDataArray): void
    {
        $depositPaymentEmailData = DepositPaymentEmailData::fromArray($depositPaymentEmailDataArray);

        self::assertEquals(
            $depositPaymentEmailDataArray[DepositPaymentEmailData::PROSPECT_LAST_NAME],
            $depositPaymentEmailData->prospectLastName()->toString(),
            'prospect last name of deposit payment email does not match expected string.'
        );
    }

    /**
     * @param array<string, mixed> $depositPaymentEmailDataArray
     * @dataProvider provideDepositPaymentEmailData
     */
    public function testProspectSalutation(array $depositPaymentEmailDataArray): void
    {
        $depositPaymentEmailData = DepositPaymentEmailData::fromArray($depositPaymentEmailDataArray);

        self::assertEquals(
            $depositPaymentEmailDataArray[DepositPaymentEmailData::PROSPECT_SALUTATION],
            $depositPaymentEmailData->prospectSalutation()?->toInt(),
            'prospect salutation of deposit payment email does not match expected expires int.'
        );
    }

    /**
     * @param array<string, mixed> $depositPaymentEmailDataArray
     * @dataProvider provideDepositPaymentEmailData
     */
    public function testUnitCollection(array $depositPaymentEmailDataArray): void
    {
        $depositPaymentEmailData = DepositPaymentEmailData::fromArray($depositPaymentEmailDataArray);

        self::assertEquals(
            $depositPaymentEmailDataArray[DepositPaymentEmailData::UNIT_COLLECTION],
            $depositPaymentEmailData->unitCollection()->toArray(),
            'unit collection of deposit payment email does not match expected array.'
        );
    }

    /**
     * @param array<string, mixed> $depositPaymentEmailDataArray
     * @dataProvider provideDepositPaymentEmailData
     */
    public function testCheckoutSessionUrl(array $depositPaymentEmailDataArray): void
    {
        $depositPaymentEmailData = DepositPaymentEmailData::fromArray($depositPaymentEmailDataArray);

        self::assertEquals(
            $depositPaymentEmailDataArray[DepositPaymentEmailData::CHECKOUT_SESSION_URL],
            $depositPaymentEmailData->checkoutSessionUrl()->toString(),
            'checkout session url of deposit payment email does not match expected string.'
        );
    }

    /**
     * @param array<string, mixed> $depositPaymentEmailDataArray
     * @dataProvider provideDepositPaymentEmailData
     */
    public function testExpiresAt(array $depositPaymentEmailDataArray): void
    {
        $depositPaymentEmailData = DepositPaymentEmailData::fromArray($depositPaymentEmailDataArray);

        self::assertEquals(
            $depositPaymentEmailDataArray[DepositPaymentEmailData::EXPIRES_AT],
            $depositPaymentEmailData->expiresAt()->toString(),
            'expires at of deposit payment email does not match expected int.'
        );
    }

    /**
     * @param array<string, mixed> $depositPaymentEmailDataArray
     * @dataProvider provideDepositPaymentEmailData
     */
    public function testToArray(array $depositPaymentEmailDataArray): void
    {
        $depositPaymentEmailData = DepositPaymentEmailData::fromArray($depositPaymentEmailDataArray);

        $this->assertEquals(
            $depositPaymentEmailDataArray,
            $depositPaymentEmailData->toArray(),
            'created deposit payment email to array does not match expected array.'
        );
    }

    public function provideDepositPaymentEmailData(): Generator
    {
        yield 'DepositPaymentEmailData data' => [
            'deposit payment email data array' => [
                DepositPaymentEmailData::RESERVATION_ID => '1234-1234-1234',
                DepositPaymentEmailData::LANGUAGE => 'de',
                DepositPaymentEmailData::PROSPECT_ID => 'ca50819f-e5a4-40d3-a425-daba3e095407',
                DepositPaymentEmailData::PROSPECT_EMAIL => 'max.mustermann@gmail.com',
                DepositPaymentEmailData::PROSPECT_FIRST_NAME => 'Max',
                DepositPaymentEmailData::PROSPECT_LAST_NAME => 'Mustermann',
                DepositPaymentEmailData::PROSPECT_SALUTATION => 0,
                DepositPaymentEmailData::UNIT_COLLECTION => [
                    [
                        'id' => 1,
                        'deposit' => 3000.00,
                        'name' => 'WE 1',
                    ],
                ],
                DepositPaymentEmailData::CHECKOUT_SESSION_URL => 'https://example.com/',
                DepositPaymentEmailData::EXPIRES_AT => '2016-06-16T16:00:00.000000',
            ],
        ];
    }
}
