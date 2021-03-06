<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\DateFormatter;

use DateTime;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\WordFormatter;
use Traversable;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\DateFormatter\FormatterChaining
 * @covers ::<protected>
 * @covers ::<private>
 */
final class FormatterChainingTest extends TestCase
{
    /**
     * @covers ::setNext
     * @covers ::getNext
     */
    public function testCanSetAndGetNext(): void
    {
        $formatter = new FormatterChainingClass();

        $next = $this->createMock(WordFormatter::class);

        $formatter->setNext($next);
        self::assertSame($next, $formatter->getNext());
    }

    /**
     * @covers ::setNext
     * @covers ::getNext
     */
    public function testCanGetWhenNoNextIsSet(): void
    {
        $formatter = new FormatterChainingClass();

        $next = $this->createMock(WordFormatter::class);

        $formatter->setNext($next);
        $formatter->setNext(null);

        self::assertNull($formatter->getNext());
    }

    /**
     * @covers ::apply
     */
    public function testCanApplyFormatter(): void
    {
        $formatter = new FormatterChainingClass();

        self::assertSame([
            '03/02/2001',
            '09/02/1987',
        ], iterator_to_array($formatter->apply([
            new DateTime('2001-02-03'),
            new DateTime('1987-02-09'),
        ]), false));
    }

    /**
     * @covers ::apply
     */
    public function testCanApplyFormatterChain(): void
    {
        $formatter = new FormatterChainingClass();

        $next = $this->createMock(WordFormatter::class);
        $next->method('apply')->willReturnCallback(
            static function (iterable $words): Traversable {
                foreach ($words as $word) {
                    yield strrev($word);
                }
            }
        );

        $formatter->setNext($next);

        self::assertSame([
            '1002/20/30',
            '7891/20/90',
        ], iterator_to_array($formatter->apply([
            new DateTime('2001-02-03'),
            new DateTime('1987-02-09'),
        ]), false));
    }
}
