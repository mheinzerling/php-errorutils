<?php
declare(strict_types = 1);

namespace mheinzerling\commons\error;

class PhpExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testError()
    {
        try {
            trigger_error("message", E_USER_ERROR);
            static::fail("error expected");
        } catch (\PHPUnit_Framework_Error $e) {
            static::assertEquals("message", $e->getMessage());
        }
    }

    public function testException()
    {
        PhpException::register();
        try {
            trigger_error("message", E_USER_ERROR);
            static::fail("error expected");
        } catch (PhpException $e) {
            static::assertEquals("message", $e->getMessage());
            static::assertEquals(__FILE__, $e->getFile());
            static::assertEquals(null, $e->getPrevious());
            static::assertEquals(E_USER_ERROR, $e->getCode());
            static::assertEquals(22, $e->getLine());
            static::assertEquals([], $e->getContext());
        } finally {
            restore_error_handler();
        }

    }

}