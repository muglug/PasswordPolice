<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\ValidationError;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\SymbolRule
 * @covers ::<protected>
 * @covers ::<private>
 */
final class SymbolRuleTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraint(): void
    {
        $rule = new SymbolRule('$%&@!', 5, null);

        // Force generation of code coverage
        $ruleConstruct = new SymbolRule('$%&@!', 5, null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMaxConstraint(): void
    {
        $rule = new SymbolRule('$%&@!', 0, 10);

        // Force generation of code coverage
        $ruleConstruct = new SymbolRule('$%&@!', 0, 10);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithBothMinAndMaxConstraint(): void
    {
        $rule = new SymbolRule('$%&@!', 5, 10);

        // Force generation of code coverage
        $ruleConstruct = new SymbolRule('$%&@!', 5, 10);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNegativeMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new SymbolRule('$%&@!', -10, null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithMaxConstraintSmallerThanMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new SymbolRule('$%&@!', 10, 5);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructUnconstrainedRule(): void
    {
        $rule = new SymbolRule('$%&@!', 0, null);

        // Force generation of code coverage
        $ruleConstruct = new SymbolRule('$%&@!', 0, null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        $rule = new SymbolRule('$%&@!', 5, 5);

        // Force generation of code coverage
        $ruleConstruct = new SymbolRule('$%&@!', 5, 5);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNoCharacters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new SymbolRule('');
    }

    /**
     * @covers ::getCharacters
     */
    public function testCanGetCharacters(): void
    {
        $rule = new SymbolRule('$%&@!');

        self::assertSame('$%&@!', $rule->getCharacters());
    }

    /**
     * @covers ::addConstraint
     */
    public function testCanAddConstraint(): void
    {
        $rule = new SymbolRule('$%&@!', 5, 5, 1);
        $rule->addConstraint(10, 10, 1);

        // Force generation of code coverage
        $ruleConstruct = new SymbolRule('$%&@!', 5, 5, 1);
        $ruleConstruct->addConstraint(10, 10, 1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::addConstraint
     */
    public function testConstraintsAreOrdered(): void
    {
        $rule = new SymbolRule('$%&@!', 5, 5, 1);
        $rule->addConstraint(10, 10, 2);

        $ruleConstruct = new SymbolRule('$%&@!', 10, 10, 2);
        $ruleConstruct->addConstraint(5, 5, 1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeSatisfied(): void
    {
        $rule = new SymbolRule('$%&@!', 2, null);

        self::assertTrue($rule->test('FOO bar $$@'));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeUnsatisfied(): void
    {
        $rule = new SymbolRule('$%&@!', 2, null);

        self::assertFalse($rule->test('FOO BAR $'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeSatisfied(): void
    {
        $rule = new SymbolRule('$%&@!', 0, 3);

        self::assertTrue($rule->test('FOO bar $$@'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeUnsatisfied(): void
    {
        $rule = new SymbolRule('$%&@!', 0, 3);

        self::assertFalse($rule->test('foo bar $$@!'));
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenConstraintWeightIsLowerThanTestWeight(): void
    {
        $rule = new SymbolRule('$%&@!', 0, 3, 1);

        self::assertTrue($rule->test('foo bar $$@!', 2));
    }

    /**
     * @covers ::validate
     */
    public function testRuleCanBeValidated(): void
    {
        $rule = new SymbolRule('$%&@!', 1, null);

        self::assertNull($rule->validate('&'));
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMinConstraintCanBeInvalidated(): void
    {
        $rule = new SymbolRule('$%&@!', 5, null);

        self::assertEquals(
            new ValidationError(
                'The password must contain at least 5 symbols ($%&@!).',
                'foo bar $$@!',
                $rule,
                1
            ),
            $rule->validate('foo bar $$@!')
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMaxConstraintCanBeInvalidated(): void
    {
        $rule = new SymbolRule('$%&@!', 0, 10);

        self::assertEquals(
            new ValidationError(
                'The password must contain at most 10 symbols ($%&@!).',
                'foo bar $$@! $$@! $$@!',
                $rule,
                1
            ),
            $rule->validate('foo bar $$@! $$@! $$@!')
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithBothMinAndMaxConstraintCanBeInvalidated(): void
    {
        $rule = new SymbolRule('$%&@!', 5, 10);

        self::assertEquals(
            new ValidationError(
                'The password must contain between 5 and 10 symbols ($%&@!).',
                'foo bar $$@!',
                $rule,
                1
            ),
            $rule->validate('foo bar $$@!')
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMaxConstraintEqualToZeroCanBeInvalidated(): void
    {
        $rule = new SymbolRule('$%&@!', 0, 0);

        self::assertEquals(
            new ValidationError(
                'The password cannot contain symbols ($%&@!).',
                'foo bar $$@!',
                $rule,
                1
            ),
            $rule->validate('foo bar $$@!')
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMinConstraintEqualToMaxConstraintCanBeInvalidated(): void
    {
        $rule = new SymbolRule('$%&@!', 3, 3);

        self::assertEquals(
            new ValidationError(
                'The password must contain exactly 3 symbols ($%&@!).',
                'foo bar $$@!',
                $rule,
                1
            ),
            $rule->validate('foo bar $$@!')
        );
    }
}
