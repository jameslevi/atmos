<?php

namespace Atmos;

abstract class Command
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

    protected $description = "No available description...";

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
     * Call the main method.
     * 
     * @param   string $method
     * @return  void
     */

    public function call(string $method = null)
    {
        if(!is_null($method))
        {
            if(method_exists($this, $method))
            {
                $this->{$method}($this->arguments);
            }
            else
            {
                Console::error("Unknown atmos command.");
            }
        }
        else
        {
            $this->main($this->arguments);
        }
    }

    /**
     * Override from the child class.
     * 
     * @param   array $arguments
     * @return  mixed
     */

    abstract protected function main(array $arguments);

}