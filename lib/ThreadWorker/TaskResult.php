<?php
namespace ThreadWorker;

class TaskResult implements \Serializable
{

    /**
     * @var array
     */
    private $result;

    public function __construct($result)
    {
        $this->result = $result;
    }

    public function serialize()
    {
        return serialize($this->result);
    }

    public function unserialize($serialized)
    {
        $this->result = unserialize($serialized);
    }

    public function getValue()
    {
        return $this->result;
    }
}
