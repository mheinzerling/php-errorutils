<?php

namespace mheinzerling\commons\error;


class PhpException extends \Exception
{
    public static function register()
    {
        error_reporting(E_ALL);
        set_error_handler(function ($code, $message, $file, $line, $context) {
            throw new PhpException($code, $message, $file, $line, $context);
        });
    }

    private $context = null;

    public function getContext()
    {
        return $this->context;
    }

    public function setContext($context)
    {
        $this->context = $context;
    }

    public function __construct($code, $message, $file, $line, $context)
    {
        parent::__construct($message, $code);

        $this->file = $file;
        $this->line = $line;
        $this->setContext($context);
    }
}