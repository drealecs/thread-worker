<?php
namespace ThreadWorker;

abstract class Task implements \Serializable
{
    /**
     * @var array
     */
    private $parameters;

    /**
     * The construct parameters used when creating a Task object will be used to call it's run() method
     */
    public function __construct()
    {
        $this->parameters = func_get_args();
    }

    public function serialize()
    {
        return serialize($this->parameters);
    }

    public function unserialize($serialized)
    {
        $this->parameters = unserialize($serialized);
    }

    public function __invoke()
    {
        return call_user_func_array(array($this, 'run'), $this->parameters);
    }

    abstract protected function run();

}
