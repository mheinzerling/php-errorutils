<?php
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

    /**
     * @return \Throwable
     */
    public function getThrowable(): \Throwable
    {
        return $this->throwable;
    }

}