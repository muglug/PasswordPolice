<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use DateTime;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use Stadly\PasswordPolice\FormerPassword;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\HashFunction\HashFunctionInterface;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\NoReuse
 * @covers ::<protected>
 * @covers ::<private>
 */
final class NoReuseTest extends TestCase
{
    /**
     * @var MockObject&HashFunctionInterface
     */
    private $hashFunction;

    /**
     * @var Password
     */
    private $password;

    protected function setUp(): void
    {
        $this->hashFunction = $this->createMock(HashFunctionInterface::class);
        $this->hashFunction->method('compare')->willReturnCallback(
            function ($password, $hash) {
                return $password === $hash;
            }
        );

        $this->password = new Password('foobar', [], [
            new FormerPassword('qwerty', new DateTime('2006-06-06')),
            new FormerPassword('baz', new DateTime('2005-05-05')),
            new FormerPassword('bar', new DateTime('2004-04-04')),
            new FormerPassword('foobar', new DateTime('2003-03-03')),
            new FormerPassword('foo', new DateTime('2002-02-02')),
            new FormerPassword('test', new DateTime('2001-01-01')),
        ]);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithCountConstraint(): void
    {
        $rule = new NoReuse($this->hashFunction, 5, 1);

        // Force generation of code coverage
        $ruleConstruct = new NoReuse($this->hashFunction, 5, 1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithFirstConstraint(): void
    {
        $rule = new NoReuse($this->hashFunction, null, 10);

        // Force generation of code coverage
        $ruleConstruct = new NoReuse($this->hashFunction, null, 10);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithBothCountAndFirstConstraint(): void
    {
        $rule = new NoReuse($this->hashFunction, 5, 10);

        // Force generation of code coverage
        $ruleConstruct = new NoReuse($this->hashFunction, 5, 10);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithCountConstraintEqualToZero(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new NoReuse($this->hashFunction, 0, 1);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNegativeCountConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new NoReuse($this->hashFunction, -10, 1);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithFirstConstraintEqualToZero(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new NoReuse($this->hashFunction, null, 0);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNegativeFirstConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new NoReuse($this->hashFunction, null, -5);
    }

    /**
     * @covers ::getCount
     */
    public function testCanGetCountConstraint(): void
    {
        $rule = new NoReuse($this->hashFunction, 5, 10);

        self::assertSame(5, $rule->getCount());
    }

    /**
     * @covers ::getFirst
     */
    public function testCanGetFirstConstraint(): void
    {
        $rule = new NoReuse($this->hashFunction, 5, 10);

        self::assertSame(10, $rule->getFirst());
    }

    /**
     * @covers ::getHashFunction
     */
    public function testCanGetHashFunction(): void
    {
        $rule = new NoReuse($this->hashFunction);

        self::assertSame($this->hashFunction, $rule->getHashFunction());
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenPasswordIsString(): void
    {
        $rule = new NoReuse($this->hashFunction, null, 1);

        self::assertTrue($rule->test('foobar'));
    }

    /**
     * @covers ::test
     */
    public function testCountConstraintCanBeSatisfied(): void
    {
        $rule = new NoReuse($this->hashFunction, 3, 1);

        self::assertTrue($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testCountConstraintCanBeUnsatisfied(): void
    {
        $rule = new NoReuse($this->hashFunction, 4, 1);

        self::assertFalse($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testFirstConstraintCanBeSatisfied(): void
    {
        $rule = new NoReuse($this->hashFunction, null, 5);

        self::assertTrue($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testFirstConstraintCanBeUnsatisfied(): void
    {
        $rule = new NoReuse($this->hashFunction, null, 4);

        self::assertFalse($rule->test($this->password));
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceDoesNotThrowExceptionWhenRuleIsSatisfied(): void
    {
        $rule = new NoReuse($this->hashFunction, 1, 1);

        $rule->enforce($this->password);

        // Force generation of code coverage
        $ruleConstruct = new NoReuse($this->hashFunction, 1, 1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceThrowsExceptionWhenRuleIsNotSatisfied(): void
    {
        $rule = new NoReuse($this->hashFunction, null, 1);

        $this->expectException(RuleException::class);

        $rule->enforce($this->password);
    }

    /**
     * @covers ::getMessage
     */
    public function testCanGetMessage(): void
    {
        $rule = new NoReuse($this->hashFunction);

        self::assertSame('Cannot reuse former passwords.', $rule->getMessage());
    }
}