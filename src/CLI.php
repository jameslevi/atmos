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
     * Store the directive strings.
     * 
     * @var array
     */

    protected $directives = array();

    /**
     * Store the description of console command.
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
     * Call the execute method.
     * 
     * @return  mixed
     */

    public function call()
    {
        return $this->execute($this->arguments);
    }

    /**
     * Return an array of directives.
     * 
     * @return  array
     */

    public function getDirectives()
    {
        return $this->directives;
    }

    /**
     * Return command description.
     * 
     * @return  string
     */

    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Override from the child class.
     * 
     * @param   array $arguments
     * @return  mixed
     */

    protected abstract function execute(array $arguments);

}