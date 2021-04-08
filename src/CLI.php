<?php

namespace Atmos;

abstract class CLI
{
    /**
     * Store arguments entered from terminal.
     * 
     * @var array
     */

    private $arguments;

    /**
     * Create a new CLI instance.
     * 
     * @param   array $arguments
     * @return  void
     */

    public function __construct(array $arguments)
    {
        $this->arguments    = $arguments;
    }

    /**
     * Call the execute method.
     * 
     * @return  void
     */

    public function call()
    {
        $this->execute($this->arguments);
    }

    /**
     * Override from the child class.
     * 
     * @param   array $arguments
     * @return  mixed
     */

    protected abstract function execute(array $arguments);

}