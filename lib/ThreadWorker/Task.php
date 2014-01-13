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
        $parameters = $this->parameters;
        if (func_num_args() > 0) {
            $executor = func_get_arg(0);
            if ($executor instanceof Executor) {
                array_push($parameters, $executor);
            }
        }
        return call_user_func_array(array($this, 'run'), $parameters);
    }
    
    private function checkImplementation()
    {
        if (!method_exists($this, 'run')) {
            throw new \Exception('Wrong implementation extending ThreadWorker\Task class. A protected or public method names run() must be defined.');
        }
    }

}
