<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\WordFormatter\Leetspeak
 * @covers ::<protected>
 * @covers ::<private>
 */
final class LeetspeakTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructFormatter(): void
    {
        $formatter = new Leetspeak();

        // Force generation of code coverage
        $formatterConstruct = new Leetspeak();
        self::assertEquals($formatter, $formatterConstruct);
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatWordWithoutLeetspeak(): void
    {
        $formatter = new Leetspeak();

        self::assertSame(['fOoBaR'], iterator_to_array($formatter->apply('fOoBaR')));
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatWordWithLeetspeak(): void
    {
        $formatter = new Leetspeak();

        self::assertContains('LEET SPEAK', iterator_to_array($formatter->apply('1337 5P34K')));
    }
}
