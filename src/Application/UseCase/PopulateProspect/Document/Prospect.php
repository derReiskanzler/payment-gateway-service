<?php

declare(strict_types=1);

namespace Allmyhomes\Application\UseCase\PopulateProspect\Document;

use Allmyhomes\Domain\Prospect\ValueObject\ProspectEmail;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectFirstName;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectLastName;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectSalutation;
use Allmyhomes\Domain\ValueObject\ProspectId;

final class Prospect
{
    private const ID = 'id';
    private const EMAIL = 'email';
    private const FIRST_NAME = 'first_name';
    private const LAST_NAME = 'last_name';
    private const SALUTATION = 'salutation';

    public function __construct(
        private ProspectId $id,
        private ProspectEmail $email,
        private ?ProspectFirstName $firstName,
        private ProspectLastName $lastName,
        private ?ProspectSalutation $salutation,
    ) {
    }

    public function id(): ProspectId
    {
        return $this->id;
    }

    public function email(): ProspectEmail
    {
        return $this->email;
    }

    public function firstName(): ?ProspectFirstName
    {
        return $this->firstName;
    }

    public function lastName(): ProspectLastName
    {
        return $this->lastName;
    }

    public function salutation(): ?ProspectSalutation
    {
        return $this->salutation;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            self::ID => $this->id()->toString(),
            self::EMAIL => $this->email()->toString(),
            self::FIRST_NAME => $this->firstName()?->toString(),
            self::LAST_NAME => $this->lastName()->toString(),
            self::SALUTATION => $this->salutation()?->toInt(),
        ];
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            ProspectId::fromString($data[self::ID]),
            ProspectEmail::fromString($data[self::EMAIL]),
            $data[self::FIRST_NAME] ? ProspectFirstName::fromString($data[self::FIRST_NAME]) : null,
            ProspectLastName::fromString($data[self::LAST_NAME]),
            $data[self::SALUTATION] ? ProspectSalutation::fromInt($data[self::SALUTATION]) : null,
        );
    }
}
