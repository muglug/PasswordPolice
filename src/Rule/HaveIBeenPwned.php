<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use Http\Discovery\Exception\NotFoundException;
use Http\Discovery\HttpClientDiscovery;
use Http\Factory\Discovery\HttpFactory;
use InvalidArgumentException;
use LogicException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use RuntimeException;
use Symfony\Component\Translation\Translator;

final class HaveIBeenPwned implements RuleInterface
{
    /**
     * @var int Minimum number of times the password can appear in breaches.
     */
    private $min;

    /**
     * @var int|null Maximum number of times the password can appear in breaches.
     */
    private $max;

    /**
     * @var ?ClientInterface HTTP client for sending requests.
     */
    private $client;

    /**
     * @var ?RequestFactoryInterface Request factory for generating HTTP requests.
     */
    private $requestFactory;

    /**
     * @param int $min Minimum number of times the password can appear in breaches.
     * @param int|null $max Maximum number of times the password can appear in breaches.
     */
    public function __construct(int $min = 0, ?int $max = 0)
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

    /**
     * @param ClientInterface $client HTTP client for sending requests.
     */
    public function setClient(ClientInterface $client): void
    {
        $this->client = $client;
    }

    /**
     * @return ClientInterface HTTP client for sending requests.
     */
    private function getClient(): ClientInterface
    {
        if (null === $this->client) {
            try {
                $this->client = HttpClientDiscovery::find();
            } catch (NotFoundException $exception) {
                throw new LogicException($exception->getMessage(), $exception->getCode(), $exception);
            }
        }
        return $this->client;
    }

    /**
     * @param RequestFactoryInterface $requestFactory Request factory for generating HTTP requests.
     */
    public function setRequestFactory(RequestFactoryInterface $requestFactory): void
    {
        $this->requestFactory = $requestFactory;
    }

    /**
     * @return RequestFactoryInterface Request factory for generating HTTP requests.
     */
    private function getRequestFactory(): RequestFactoryInterface
    {
        if (null === $this->requestFactory) {
            try {
                $this->requestFactory = HttpFactory::requestFactory();
            } catch (RuntimeException $exception) {
                throw new LogicException($exception->getMessage(), $exception->getCode(), $exception);
            }
        }
        return $this->requestFactory;
    }

    /**
     * @return int Minimum number of times the password can appear in breaches.
     */
    public function getMin(): int
    {
        return $this->min;
    }

    /**
     * @return int|null Maximum number of times the password can appear in breaches.
     */
    public function getMax(): ?int
    {
        return $this->max;
    }

    /**
     * {@inheritDoc}
     */
    public function test(string $password): bool
    {
        $count = $this->getCount($password);

        if ($count < $this->min) {
            return false;
        }

        if (null !== $this->max && $this->max < $count) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function enforce(string $password, Translator $translator): void
    {
        if (!$this->test($password)) {
            throw new RuleException($this, $this->getMessage($translator));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getMessage(Translator $translator): string
    {
        if ($this->getMax() === null) {
            return $translator->transChoice(
                'Must appear at least once in breaches.|'.
                'Must appear at least %count% times in breaches.',
                $this->getMin()
            );
        }

        if ($this->getMax() === 0) {
            return $translator->trans(
                'Must not appear in any breaches.'
            );
        }

        if ($this->getMin() === 0) {
            return $translator->transChoice(
                'Must appear at most once in breaches.|'.
                'Must appear at most %count% times in breaches.',
                $this->getMax()
            );
        }

        if ($this->getMin() === $this->getMax()) {
            return $translator->transChoice(
                'Must appear exactly once in breaches.|'.
                'Must appear exactly %count% times in breaches.',
                $this->getMin()
            );
        }

        return $translator->trans(
            'Must appear between %min% and %max% times in breaches.',
            ['%min%' => $this->getMin(), '%max%' => $this->getMax()]
        );
    }

    /**
     * @param string $password Password to check in breaches.
     * @return int Number of times the password appears in breaches.
     * @throws TestException If an error occurred while using the Have I Been Pwned? service.
     */
    private function getCount(string $password): int
    {
        $sha1 = strtoupper(sha1($password));
        $prefix = substr($sha1, 0, 5);
        $suffix = substr($sha1, 5, 35);

        $requestFactory = $this->getRequestFactory();
        $request = $requestFactory->createRequest('GET', 'https://api.pwnedpasswords.com/range/'.$prefix);

        $client = $this->getClient();

        try {
            $response = $client->sendRequest($request);
            $body = $response->getBody();
            $contents = $body->getContents();
            $lines = explode("\r\n", $contents);
            foreach ($lines as $line) {
                if (substr($line, 0, 35) === $suffix) {
                    return (int)substr($line, 36);
                }
            }
            return 0;
        } catch (ClientExceptionInterface | RuntimeException $exception) {
            throw new TestException($this, 'An error occurred while using the Have I Been Pwned? service.', $exception);
        }
    }
}