<?php
declare(strict_types = 1);

namespace mheinzerling\commons\error;

class ErrorMailerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var bool
     */
    static $visited = false;

    public function testSuccess()
    {
        static::$visited = false;
        (new ErrorMailer("nobody@example.com", true))->call(function () {
            static::$visited = true;
        }, null, null);
        self::assertEquals(true, static::$visited);
    }

    public function testExceptionBeforeSendingError()
    {
        static::$visited = false;
        (new ErrorMailer("nobody@example.com", true))->call(function () {
            throw new \Exception("Boom");
        }, function () {
            static::$visited = true;
        }, null);
        self::assertEquals(true, static::$visited);
    }

    public function testExceptionAfterSendingError()
    {
        static::$visited = false;
        (new ErrorMailer("nobody@example.com", true))->call(function () {
            throw new \Exception("Boom");
        }, null, function () {
            static::$visited = true;
        });
        self::assertEquals(true, static::$visited);
    }

    public function testException()
    {
        $expected = "^\[0\]&nbsp;Boom<br \/>\s+" .
            "\#\#&nbsp;.*?ErrorMailerTest\.php\(\d+\)<br \/>\s+" .
            "\#.*?\s+" .
            "<br />\s+" .
            "SERVER<br />\s+" .
            "array&nbsp;\(.*?\)<br />\s+";
        $this->expectOutputRegex('@' . $expected . '@s');
        (new ErrorMailer("nobody@example.com", true))->call(function () {
            throw new \Exception("Boom");
        }, null, null);
    }

    public function testExceptionBeforeAndAfter()
    {
        $expected = "^\[0\]&nbsp;BoomBefore<br \/>\s+" .
            "\#\#&nbsp;.*?ErrorMailerTest\.php\(\d+\)<br \/>\s+" .
            "\#.*?\s+" .
            "<br />\s+" .
            "SERVER<br />\s+" .
            "array&nbsp;\(.*?\)<br />\s+" .

            "\[0\]&nbsp;BoomCallback<br \/>\s+" .
            "\#\#&nbsp;.*?ErrorMailerTest\.php\(\d+\)<br \/>\s+" .
            "\#.*?\s+" .
            "<br />\s+" .
            "SERVER<br />\s+" .
            "array&nbsp;\(.*?\)<br />\s+" .

            "\[0\]&nbsp;BoomAfter<br \/>\s+" .
            "\#\#&nbsp;.*?ErrorMailerTest\.php\(\d+\)<br \/>\s+" .
            "\#.*?\s+" .
            "<br />\s+" .
            "SERVER<br />\s+" .
            "array&nbsp;\(.*?\)<br />\s+$";
        $this->expectOutputRegex('@' . $expected . '@s');


        (new ErrorMailer("nobody@example.com", true))->call(function () {
            throw new \Exception("BoomCallback");
        }, function () {
            throw new \Exception("BoomBefore");
        }, function () {
            throw new \Exception("BoomAfter");
        });
    }

    public function testExceptionProcessed()
    {
        $expected = "^\[0\]&nbsp;Boom<br \/>\s+" .
            "\#\#&nbsp;.*?ErrorMailerTest\.php\(\d+\)<br \/>\s+" .
            "\#.*?\s+" .
            "<br />\s+" .
            "SERVER<br />\s+" .
            "array&nbsp;\(.*?\)<br />\s+$";
        $this->expectOutputRegex('@' . $expected . '@s');

        static::$visited = false;
        (new ErrorMailer("nobody@example.com", true))->call(function () {
            try {
                throw new \Exception("Boom");
            } catch (\Exception $e) {
                throw new ProcessedThrowable($e);
            }
        }, function () {
            static::$visited = true;
        }, function () {
            static::$visited = true;
        });
        self::assertEquals(false, static::$visited);
    }


}