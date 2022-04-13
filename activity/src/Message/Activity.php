<?php

declare(strict_types=1);

namespace App\Message;

use DateTimeImmutable;
use DateTimeInterface;

final class Activity
{
     public function __construct(
         public readonly string $url,
         public readonly  DateTimeInterface $dateTime = new DateTimeImmutable()
     )
     {
     }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDateTime(): DateTimeInterface
    {
        return $this->dateTime;
    }
}
