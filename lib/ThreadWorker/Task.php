<?php
namespace ThreadWorker;

abstract class Task implements \Serializable{

    /**
     * @var array
     */
    private $parameters;

    public function __construct($parameters = array())
    {
        $this->parameters = (array)$parameters;
    }

    public function serialize()
    {
        return serialize($this->parameters);
    }

    public function unserialize($serialized)
    {
        $this->parameters = unserialize($serialized);
    }

    public function getParameter($name)
    {
        if (isset($this->parameters[$name])) {
            return $this->parameters[$name];
        }
        return null;
    }

    public abstract function run();

}
