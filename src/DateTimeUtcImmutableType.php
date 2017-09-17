<?php
/**
 * Copyright 2017 LitGroup, LLC
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

declare(strict_types=1);

namespace LitGroup\Doctrine\DBAL\UTC;

use DateTimeZone;
use DateTimeImmutable;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeImmutableType;

class DateTimeUtcImmutableType extends DateTimeImmutableType
{
    public const TYPE_NAME = 'datetime_utc_immutable';

    public function getName()
    {
        return self::TYPE_NAME;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return $value;
        }

        return parent::convertToDatabaseValue($value->setTimeZone($this->getUtcTimezone()), $platform);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return $value;
        }

        if ($value instanceof DateTimeImmutable) {
            return $value->setTimezone($this->getUtcTimezone());
        }

        $dateTime =  DateTimeImmutable::createFromFormat(
            $platform->getDateTimeFormatString(), $value, $this->getUtcTimezone());

        if ($dateTime === false) {
            return null;
        }

        return $dateTime;
    }

    private function getUtcTimezone(): DateTimeZone
    {
        return new DateTimeZone('UTC');
    }
}