<?php

namespace Graphite\Component\Atmos;

abstract class Command
{
    /**
     * Store arguments entered from terminal.
     * 
     * @var array
     */
    private $arguments;

    /**
     * Other keyword for calling this command.
     * 
     * @var string
     */
    protected $alias;

    /**
     * Something that describes this command.
     * 
     * @var string
     */
    protected $description = "No available description...";

    /**
     * Create a new command class instance.
     * 
     * @param   array $arguments
     * @return  void
     */
    public function __construct(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * Return argument array.
     * 
     * @return  array
     */
    public function getArguments()
    {
        return $this->arguments;
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
     * Return command alias.
     * 
     * @return  string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Call the main method.
     * 
     * @param   string $method
     * @return  void
     */
    public function call(string $method = null)
    {
        $args = $this->arguments;
        $this->onBeforeExecute($args);

        if(!is_null($method))
        {
            if(method_exists($this, $method))
            {
                $this->{$method}($args);
                $this->onAfterExecute($args);
            }
            else
            {
                $this->onError($args);
                Console::error("Unknown atmos command.");
            }
        }
        else
        {
            $this->main($args);
            $this->onAfterExecute($args);
        }
    }

    /**
     * Override from the child class.
     * 
     * @param   array $arguments
     * @return  void
     */
    abstract protected function main(array $arguments);

    /**
     * Method called before command execution.
     * 
     * @param   array $arguments
     * @return  void
     */
    protected function onBeforeExecute(array $arguments) {}

    /**
     * Method called after command execution.
     * 
     * @param   array $arguments
     * @return  void
     */
    protected function onAfterExecute(array $arguments) {}

    /**
     * Method called when error occurred.
     * 
     * @param   array $arguments
     * @return  void
     */
    protected function onError(array $arguments) {}

}