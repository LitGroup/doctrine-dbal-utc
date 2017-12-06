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

namespace Test\LitGroup\Doctrine\DBAL\UTC;

use DateTimeImmutable;
use DateTime;
use DateTimeZone;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Types\DateTimeImmutableType;
use Doctrine\DBAL\Types\Type;
use LitGroup\Doctrine\DBAL\UTC\DateTimeUtcImmutableType;
use PHPUnit\Framework\TestCase;

class DateTimeUtcImmutableTypeTest extends TestCase
{
    private static $systemTimeZone;

    /** @var DateTimeUtcImmutableType */
    private $type;

    public static function setUpBeforeClass()
    {
        self::$systemTimeZone = ini_get('date.timezone');
        ini_set('date.timezone', 'America/Denver');
    }

    public static function tearDownAfterClass()
    {
        ini_set('date.timezone', self::$systemTimeZone);
    }

    /**
     * @throws DBALException
     */
    protected function setUp(): void
    {
        if (!Type::hasType(DateTimeUtcImmutableType::TYPE_NAME)) {
            Type::addType(DateTimeUtcImmutableType::TYPE_NAME, DateTimeUtcImmutableType::class);
        }

        $this->type = Type::getType(DateTimeUtcImmutableType::TYPE_NAME);
    }

    function testInstance(): void
    {
        $this->assertInstanceOf(DateTimeImmutableType::class, $this->type);
    }

    function testTypeName(): void
    {
        $this->assertSame(DateTimeUtcImmutableType::TYPE_NAME, $this->type->getName());
    }

    function getConversionToDatabaseValueExamples(): array
    {
        return [
            [
                null,
                null
            ],
            [
                DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', '2017-09-17 20:30:45', new DateTimeZone('UTC')),
                '2017-09-17 20:30:45'
            ],
            [
                DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', '2017-09-17 23:30:45', new DateTimeZone('Europe/Moscow')),
                '2017-09-17 20:30:45'
            ],
        ];
    }

    /**
     * @dataProvider getConversionToDatabaseValueExamples
     */
    function testConversionToDatabaseValue(?DateTimeImmutable $dateTime, ?string $str): void
    {
        $this->assertSame(
            $str,
            $this->type->convertToDatabaseValue($dateTime, $this->createPlatform())
        );
    }

    function testNullConversionToPHPValue(): void
    {
        $this->assertNull($this->type->convertToPHPValue(null, $this->createPlatform()));
    }

    function testDateTimeConversionToPHPValue(): void
    {
        $input = new DateTimeImmutable();

        $converted = $this->type->convertToPHPValue($input, $this->createPlatform());
        $this->assertEquals($input, $converted);
        $this->assertEquals('UTC', $converted->getTimezone()->getName());
    }

    function testConversionToPHPValue(): void
    {
        /** @var DateTimeImmutable $value */
        $value = $this->type->convertToPHPValue('2017-09-17 20:30:45', $this->createPlatform());

        $this->assertInstanceOf(DateTimeImmutable::class, $value);
        $this->assertEquals(
            DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', '2017-09-17 20:30:45', new \DateTimeZone('UTC')),
            $value
        );
        $this->assertEquals('UTC', $value->getTimezone()->getName());
    }

    function getInvalidDatabaseValueExamples(): array
    {
        return [
            [''],
            ['invalid datetime'],
        ];
    }

    /**
     * @dataProvider getInvalidDatabaseValueExamples
     */
    function testInvalidConversionToPHPValue(string $value): void
    {
        $this->assertNull($this->type->convertToPHPValue($value, $this->createPlatform()));
    }

    private function createPlatform(): FakePlatform
    {
        return new FakePlatform();
    }
}