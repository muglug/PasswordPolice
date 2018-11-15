<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use Stadly\PasswordPolice\Rule;
use Stadly\PasswordPolice\RuleException;
use Symfony\Component\Translation\Translator;

final class UpperCase implements Rule
{
    /**
     * @var int Minimum number of upper case letters in password.
     */
    private $min;

    /**
     * @var int|null Maximum number of upper case letters in password.
     */
    private $max;

    public function __construct(int $min, ?int $max = null)
    {
        if ($min < 0) {
            throw new InvalidArgumentException('Min cannot be negative.');
        }
        if ($max !== null && $max < $min) {
            throw new InvalidArgumentException('Max cannot be smaller than min.');
        }
        if ($min === 0 && $max === null) {
            throw new InvalidArgumentException('Min cannot be zero when max is unconstrained.');
        }

        $this->min = $min;
        $this->max = $max;
    }

    public function getMin(): int
    {
        return $this->min;
    }

    public function getMax(): ?int
    {
        return $this->max;
    }

    public function test(string $password): bool
    {
        if ($this->getCount($password) < $this->min) {
            return false;
        }

        if (null !== $this->max && $this->max < $this->getCount($password)) {
            return false;
        }

        return true;
    }

    /**
     * @throws RuleException If the rule cannot be enforced.
     */
    public function enforce(string $password, Translator $translator): void
    {
        if (!$this->test($password)) {
            throw new RuleException($this, $this->getMessage($translator));
        }
    }

    public function getMessage(Translator $translator): string
    {
        if ($this->getMax() === null) {
            return $translator->transChoice(
                'There must be at least one upper case character.|'.
                'There must be at least %count% upper case characters.',
                $this->getMin()
            );
        }

        if ($this->getMax() === 0) {
            return $translator->trans(
                'There must be no upper case characters.'
            );
        }

        if ($this->getMin() === 0) {
            return $translator->transChoice(
                'There must be at most one upper case character.|'.
                'There must be at most %count% upper case characters.',
                $this->getMax()
            );
        }

        if ($this->getMin() === $this->getMax()) {
            return $translator->transChoice(
                'There must be exactly one upper case character.|'.
                'There must be exactly %count% upper case characters.',
                $this->getMin()
            );
        }

        return $translator->trans(
            'There must be between %min% and %max% upper case characters.',
            ['%min%' => $this->getMin(), '%max%' => $this->getMax()]
        );
    }

    private function getCount(string $password): int
    {
        $lowerCase = mb_strtolower($password);

        $passwordCharacters = $this->splitString($password);
        $lowerCaseCharacters = $this->splitString($lowerCase);
        assert(count($passwordCharacters) === count($lowerCaseCharacters));

        $count = 0;
        for ($i = count($passwordCharacters)-1; $i >= 0; --$i) {
            if ($passwordCharacters[$i] !== $lowerCaseCharacters[$i]) {
                ++$count;
            }
        }

        return $count;
    }

    private function splitString(string $string): array
    {
        $characters = preg_split('{}u', $string, -1, PREG_SPLIT_NO_EMPTY);
        assert($characters !== false);

        return $characters;
    }
}
