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
     * Something that describes this command.
     * 
     * @var string
     */

    protected $description;

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
     * Return the description of the command.
     * 
     * @return  string
     */

    public function getDescription()
    {
        return $this->description;
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