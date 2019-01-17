<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\WordFormatter\Capitalize
 * @covers ::<protected>
 * @covers ::<private>
 */
final class CapitalizeTest extends TestCase
{
    /**
     * @covers ::apply
     */
    public function testCanFormatWord(): void
    {
        $formatter = new Capitalize();

        self::assertSame(['Foobar'], iterator_to_array($formatter->apply('fOoBaR')));
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatUtf8Characters(): void
    {
        $formatter = new Capitalize();

        self::assertSame(['Ááæøôëñ'], iterator_to_array($formatter->apply('áÁæØôËñ')));
    }
}
