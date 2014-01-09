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
        $this->checkImplementation();
        $this->parameters = func_get_args();
    }

    public function serialize()
    {
        return serialize($this->parameters);
    }

    public function unserialize($serialized)
    {
        $this->checkImplementation();
        $this->parameters = unserialize($serialized);
    }

    public function __invoke()
    {
        return call_user_func_array(array($this, 'run'), $this->parameters);
    }
    
    private function checkImplementation()
    {
        if (!method_exists($this, 'run')) {
            throw new \Exception('Wrong implementation extending ThreadWorker\Task class. A protected or public method names run() must be defined.');
        }
    }

}
