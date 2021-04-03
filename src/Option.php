<?php

namespace Atmos;

use Closure;

class Option
{
    /**
     * Store option directives.
     * 
     * @var array
     */

    private $directives;

    /**
     * Store the script or closure to execute.
     * 
     * @var mixed
     */

    private $command;

    /**
     * Store option description.
     * 
     * @var string
     */

    private $description;

    /**
     * Construct a new option class.
     * 
     * @param   array $directives
     * @param   string $description
     * @param   mixed $command
     * @return  void
     */

    public function __construct(array $directives, string $description, $command)
    {
        $this->directives       = $directives;
        $this->description      = $description;
        $this->command          = $command;
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
     * Return the options description.
     * 
     * @return  string
     */

    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Check if option has matched a directive.
     * 
     * @param   string $directive
     * @return  bool
     */

    public function hasDirective(string $directive)
    {
        return in_array($directive, $this->directives, true);
    }

    /**
     * Execute the command from this option.
     * 
     * @param   array $arguments
     * @return  void
     */

    public function execute(array $arguments)
    {
        $command = $this->command;

        if($command instanceof Closure)
        {
            $command($arguments);
        }
        else if($command instanceof CLI)
        {
            $command->call();
        }

        return $this;
    }

}