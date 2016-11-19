<?php

namespace mheinzerling\commons\error;


class ErrorMailer
{
    /**
     * @var string
     */
    private $recipient;
    /**
     * @var bool
     */
    private $debug;

    public function __construct(string $recipient, bool $debug)
    {
        $this->recipient = $recipient;
        $this->debug = $debug;
    }

    private function export(string $label, array $arr = null): string
    {
        if ($arr == null) return "";
        $message = $label . "\n";
        $message .= var_export($arr, true) . "\n";
        return $message;
    }

    /**
     * @param callable $callback
     * @param callable|null $beforeSendingError i.e. try to set http error header etc.ant
     * @param callable|null $afterSendingError fulfil a last will before terminating the script
     * @return void
     */
    public function call(callable $callback, callable $beforeSendingError = null, callable $afterSendingError = null) //:void
    {
        try {
            $callback();
        } catch (ProcessedThrowable $p) {
            $this->send($p, "Unexpected error");
        } catch (\Throwable $t) {
            try {
                if ($beforeSendingError != null) $beforeSendingError();
            } catch (\Throwable $t3) {
                $this->send($t3, "Unexpected critical error in beforeSendingError");
            }
            $this->send($t, "Unexpected critical error");
            try {
                if ($afterSendingError != null) $afterSendingError();
            } catch (\Throwable $t2) {
                $this->send($t2, "Unexpected critical error in afterSendingError");
            }
        }
    }

    /**
     * @param \Throwable $throwable
     * @param string $subject
     * @return void
     */
    private function send(\Throwable $throwable, string $subject) //:void
    {
        $message = $this->toMessage($throwable);
        if ($this->debug) {
            echo(nl2br(str_replace(" ", "&nbsp;", $message)));
            return;
        }
        mail($this->recipient, $subject, $message);
    }

    /**
     * @param \Throwable $throwable
     * @return string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function toMessage(\Throwable $throwable):string
    {
        if ($throwable instanceof ProcessedThrowable) $throwable = $throwable->getThrowable();
        $message = "[" . $throwable->getCode() . "] " . $throwable->getMessage() . "\n";
        $message .= "## " . $throwable->getFile() . "(" . $throwable->getLine() . ")\n";
        $message .= $throwable->getTraceAsString() . "\n";
        $message .= "Previous: " . $throwable->getPrevious() . "\n";
        $message .= "\n";
        $message .= $this->export("GET", $_GET);
        $message .= $this->export("POST", $_POST);
        $message .= $this->export("SESSION", isset($_SESSION) ? $_SESSION : null);
        $message .= $this->export("COOKIE", $_COOKIE);
        $message .= $this->export("FILES", $_FILES);
        $message .= $this->export("SERVER", $_SERVER);
        $message .= $this->export("ENV", $_ENV);
        return $message;
    }

}