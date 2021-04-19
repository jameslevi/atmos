<?php

namespace Atmos;

class Console
{
    /**
     * Types of console messages.
     * 
     * @var array
     */

    private $types = array(
        'success'  => "\e[32m",
        'warn'     => "\e[33m",
        'error'    => "\e[31m",
        'info'     => "\e[34m",
    );

    /**
     * Type of console messages.
     * 
     * @var string
     */

    private $type;

    /**
     * Store the message to be displayed.
     * 
     * @var string
     */

    private $message;

    /**
     * Append line break after each line.
     * 
     * @var bool
     */

    private $eol;

    /**
     * Create new instance of console.
     * 
     * @param   string $message
     * @param   string $type
     * @return  void
     */

    public function __construct(string $message, string $type = 'log', bool $eol = true)
    {
        $this->message      = $message;
        $this->type         = strtolower($type);
        $this->eol          = $eol;
    }

    /**
     * Return the type of the console message.
     * 
     * @return  string
     */

    public function getType()
    {
        return $this->type;
    }

    /**
     * Return console message to display.
     * 
     * @return  string
     */

    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Print the message in to the terminal.
     * 
     * @return  void
     */

    public function print()
    {
        $type = $this->type;

        // Set the text color.
        if(array_key_exists($type, $this->types))
        {
            echo $this->types[$type];
        }

        echo $this->message;

        // Append line break at the end of line.
        if($this->eol)
        {
            echo PHP_EOL;
        }

        // Reset the text color in to default value.
        echo "\e[39m"; 
    }

    /**
     * Create a new message log instance.
     * 
     * @param   string $message
     * @param   bool $eol
     * @return  \Atmos\Console
     */

    public static function log(string $message, bool $eol = true)
    {
        $instance = new self($message, 'log', $eol);
        $instance->print();

        return $instance;
    }

    /**
     * Create a new success console message.
     * 
     * @param   string $message
     * @param   bool $eol
     * @return  \Atmos\Console
     */

    public static function success(string $message, bool $eol = true)
    {
        $instance = new self($message, 'success', $eol);
        $instance->print();

        return $instance;
    }

    /**
     * Create a new info console message.
     * 
     * @param   string $message
     * @param   bool $eol
     * @return  \Atmos\Console
     */

    public static function info(string $message, bool $eol = true)
    {
        $instance = new self($message, 'info', $eol);
        $instance->print();

        return $instance;
    }

    /**
     * Create a new console warning message.
     * 
     * @param   string $message
     * @param   bool $eol
     * @return  \Atmos\Console
     */

    public static function warn(string $message, bool $eol = true)
    {
        $instance = new self($message, 'warn', $eol);
        $instance->print();

        return $instance;
    }

    /**
     * Create a new error console message.
     * 
     * @param   string $message
     * @param   bool $eol
     * @return  \Atmos\Console
     */

    public static function error(string $message, bool $eol = true)
    {
        $instance = new self($message, 'error', $eol);
        $instance->print();
        
        return $instance;
    }

    /**
     * Create a new line break message.
     * 
     * @return  \Atmos\Console
     */

    public static function lineBreak()
    {
        $instance = new self("");
        $instance->print();

        return $instance;
    }

    /**
     * Execute one or more commands.
     * 
     * @param   mixed $commands
     * @return  void
     */

    public static function call($commands)
    {
        if(is_string($commands))
        {
            $commands = [$commands];
        }

        $output = array();

        foreach($commands as $command)
        {
            $feedback = null;

            exec($command, $feedback);

            $output[] = array(
                'command'           => $command,
                'feedback'          => $feedback,
            );
        }

        for($i = 0; $i <= sizeof($output) - 1; $i++)
        {
            $data       = $output[$i];
            $command    = $data['command'];
            $messages   = $data['feedback'];

            Console::info("> " . $command);

            for($j = 0; $j <= sizeof($messages) - 1; $j++)
            {
                echo $messages[$j];

                if($j != (sizeof($messages) - 1))
                {
                    echo PHP_EOL;
                }
            }

            if($i != (sizeof($output) - 1))
            {
                echo PHP_EOL;
            }
        }
    }

}