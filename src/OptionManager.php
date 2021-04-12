<?php

namespace Atmos;

abstract class OptionManager
{
    /**
     * Store the registered options.
     * 
     * @var array
     */

    private $options = array();

    /**
     * Store the option object to execute.
     * 
     * @var \Atmos\Option
     */

    private $option;

    /**
     * Register a new option.
     * 
     * @param   array $parameters
     * @param   mixed $command
     * @return  $this
     */

    protected function register(array $parameters, $command)
    {
        $id                 = $parameters['id'];
        $directives         = $parameters['directives'] ?? [];
        $description        = $parameters['description'] ?? null;
 
        $this->options[] = new Option($id, $directives, $command, $description);

        return $this;
    }

    /**
     * Check if arguments appears from registered options.
     * 
     * @param   string $directive
     * @return  bool
     */

    protected function matchArguments(string $directive)
    {
        $matched = false;

        for($i = 0; $i <= (sizeof($this->options) - 1); $i++)
        {
            $option = $this->options[$i];

            if($option->hasDirective($directive))
            {
                $matched            = true;
                $this->option       = $option;
                break;
            }
        }

        return $matched;
    }

    /**
     * Return the current option object.
     * 
     * @return  \Atmos\Option
     */

    public function getOption()
    {
        return $this->option;
    }

    /**
     * List all registered options.
     * 
     * @return  void
     */

    public function listAllOptions()
    {
        $limit = 36;

        for($i = 0; $i <= (sizeof($this->options) - 1); $i++)
        {
            $option         = $this->options[$i];

            $directives     = implode(', ', $option->getDirectives());
            $length         = strlen($directives) + 4;
            $message        = "    " . $directives;
            $spaces         = 0;

            if($length < $limit)
            {
                $spaces = $limit - $length;
            }

            for($j = 1; $j <= $spaces; $j++)
            {
                $message .= " ";
            }

            $message .= "- ";

            Console::success($message, false);
            Console::log($option->getDescription());
        }
    }

}