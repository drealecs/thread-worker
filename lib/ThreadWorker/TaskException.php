<?php
namespace ThreadWorker;

final class TaskException extends \Exception implements \Serializable
{
    public function __construct(\Exception $exception)
    {
        parent::__construct($exception->getMessage(), $exception->getCode(), $exception);
        $this->code = $exception->getCode();
        $this->message = $exception->getMessage();
        $this->file = $exception->getFile();
        $this->line = $exception->getLine();
    }

    public function serialize()
    {
        return serialize(array($this->code, $this->message, $this->file, $this->line));
    }

    public function unserialize($serialized)
    {
        list($this->code, $this->message, $this->file, $this->line) = unserialize($serialized);
    }
}
