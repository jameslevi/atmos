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
     * Store command id.
     * 
     * @var string
     */

    private $id;

    /**
     * Construct a new option class.
     * 
     * @param   string $id
     * @param   array $directives
     * @param   mixed $command
     * @param   string $description
     * @return  void
     */

    public function __construct(string $id, array $directives, $command, string $description = null)
    {
        $this->id               = $id;
        $this->directives       = $directives;
        $this->command          = $command;
        $this->description      = $description;
    }

    /**
     * Return option id.
     * 
     * @return  string
     */

    public function getId()
    {
        return $this->id;
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
        else if($command instanceof Command)
        {
            $command->call();
        }

        return $this;
    }

}