<?php
declare(strict_types = 1);
namespace mheinzerling\commons\error;


class ProcessedThrowable extends \Exception
{
    /**
     * @var \Throwable
     */
    private $throwable;

    public function __construct(\Throwable $throwable)
    {
        $this->throwable = $throwable;
    }

    public function getThrowable(): \Throwable
    {
        return $this->throwable;
    }

}