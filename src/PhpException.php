<?php

namespace mheinzerling\commons\error;


class PhpException extends \Exception
{
    /**
     * @return void
     */
    public static function register() //:void
    {
        error_reporting(E_ALL);
        set_error_handler(function (int $code, string $message, string $file = null, int $line = null, array $context = null) {
            throw new PhpException($code, $message, $file, $line, $context);
        });
    }

    /**
     * @var array|null
     */
    private $context = null;

    public function __construct(int $code, string $message, string $file = null, int $line = null, array $context = null)
    {
        parent::__construct($message, $code);

        $this->file = $file;
        $this->line = $line;
        $this->setContext($context);
    }

    /**
     * @return array|null
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param array|null $context
     * @return void
     */
    public function setContext(array $context = null) //:void
    {
        $this->context = $context;
    }
}