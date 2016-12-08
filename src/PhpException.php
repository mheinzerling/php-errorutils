<?php
declare(strict_types = 1);

namespace mheinzerling\commons\error;


class PhpException extends \Exception
{
    public static function register(): void
    {
        error_reporting(E_ALL);
        set_error_handler(function (int $code, string $message, string $file = null, int $line = null, array $context = null) {
            throw new PhpException($code, $message, $file, $line, $context);
        });
    }

    /**
     * @var ?array
     */
    private $context = null;

    public function __construct(int $code, string $message, ?string $file = null, ?int $line = null, ?array $context = null)
    {
        parent::__construct($message, $code);

        $this->file = $file;
        $this->line = $line;
        $this->setContext($context);
    }

    public function getContext():?array
    {
        return $this->context;
    }

    public function setContext(?array $context): void
    {
        $this->context = $context;
    }
}