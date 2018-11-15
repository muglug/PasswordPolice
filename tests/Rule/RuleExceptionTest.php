<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\RuleException
 * @covers ::<protected>
 * @covers ::<private>
 */
final class RuleExceptionTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructException(): void
    {
        $exception = new RuleException(new UpperCase(5), 'foo');

        // Force generation of code coverage
        $exceptionConstruct = new RuleException(new UpperCase(5), 'foo');
        self::assertEquals($exception, $exceptionConstruct);
    }

    /**
     * @covers ::getRule
     */
    public function testCanGetRule(): void
    {
        $rule = new UpperCase(5);
        $exception = new RuleException($rule, 'foo');

        self::assertSame($rule, $exception->getRule());
    }
}
