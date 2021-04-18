<?php

namespace Atmos;

use Stencil\Comment;
use Stencil\Method;
use Stencil\Stencil;

class CommandLine extends OptionManager
{
    /**
     * Current atmos version.
     * 
     * @var string
     */

    private $version = '1.0.2';

    /**
     * Store the command line instance here.
     * 
     * @var \Atmos\CommandLine
     */

    private static $instance;

    /**
     * Store command line arguments from the
     * user terminal.
     * 
     * @var array
     */

    private $arguments;

    /**
     * Check if arguments are already executed.
     * 
     * @var bool
     */

    private $executed = false;

    /**
     * Check if command line is still running.
     * 
     * @var bool
     */

    private $running = false;

    /**
     * Check if command line script has ended
     * and ready to terminate.
     * 
     * @var bool
     */

    private $ended = false;

    /**
     * Store the atmos config.
     * 
     * @var array
     */

    private $config;

    /**
     * Create a new command line instance.
     * 
     * @param   array $argv
     * @param   array $config
     * @return  void
     */

    private function __construct(array $argv, array $config)
    {
        array_shift($argv);
    
        $this->arguments        = $argv;
        $this->config           = $config;
    }

    /**
     * Return current atmos version.
     * 
     * @return  string
     */

    public function version()
    {
        return $this->version;
    }

    /**
     * Return config data by key or just the whole data.
     * 
     * @param   string $key
     * @return  mixed
     */

    public function config(string $key = null)
    {
        if(!is_null($key) && array_key_exists($key, $this->config))
        {
            return $this->config[$key];
        }
        else
        {
            return $this->config;
        }
    }

    /**
     * Execute the arguments from the command line.
     * 
     * @return  void
     */

    public function exec()
    {
        if(!$this->executed)
        {
            $this->executed = true;
            $this->runtime();
        }
    }

    /**
     * Register atmos built-in option commands.
     * 
     * @return  void
     */

    private function registerDefaultOptions()
    {
        $that = $this;

        $this->register([
            'id'            => 'version',
            'description'   => 'Return the current version of ATMOS CLI.',
            'directives'    => ['-v', '--version'],
        ], function($args) use ($that) {

            Console::log("current release \e[33mv" . $that->version);

        });

        $this->register([
            'id'            => 'clear',
            'description'   => 'Clear the terminal screen.',
            'directives'    => ['-x', '--clear'],
        ], function($args) {

            print("\033[2J\033[;H");

        });

        $this->register([
            'id'            => 'help',
            'description'   => 'Return list of all built-in options.',
            'directives'    => ['-h', '--help'],
        ], function($args) use ($that) {

            Console::log("ATMOS CLI \e[33mv" . $that->version);
            Console::log("Is a simple library for creating command line scripts in PHP.");
            Console::lineBreak();
            Console::warn("Usage:");
            Console::log("    php atmos [directive] [param1] [param2] ...");
            Console::lineBreak();
            Console::warn("Options:");

            $that->listAllOptions();
            Console::lineBreak();

        });

        $this->register([
            'id'            => 'make',
            'description'   => 'Generate a new PHP command class file.',
            'directives'    => ['-m', '--make'],
        ], function($args) use ($that) {

            if(!empty($args))
            {
                if(!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $args[0]))
                {
                    $file = ucfirst($args[0]);
                    $path = $that->config('directory') . '/' . $file . '.php';

                    if(!file_exists($path))
                    {
                        $template = new Stencil($file);
                        $template->setNamespace("Atmos\Console");
                        $template->extends("\Atmos\CLI");
                        $template->setIndention(1);

                        $template->addComment(Comment::makeStringVar("Something that will describe your command."));
                        $template->lineBreak();
                        $template->addProtectedVariable("description", "No available description...");
                        $template->lineBreak();
                        $template->addComment(Comment::makeMethod("Method to be executed in the command line.")->addArrayParam("arguments"));
                        $template->lineBreak();
                        $template->addMethod(Method::makeProtected("execute")->addArrayParam("arguments"));
                        $template->lineBreak();

                        $template->generate($that->config('directory') . '/');

                        Console::success("New console file was successfully created.");
                    }
                    else
                    {
                        Console::error("Command file already exists.");
                    }
                }
                else
                {
                    Console::error("Filename must not contain special characters.");
                }
            }
            else
            {
                Console::error("Please enter filename to create new command file.");
            }
        });

        $this->register([
            'id'            => 'serve',
            'description'   => 'Start the built-in PHP server.',
            'directives'    => ['-s', '--serve'],
        ], function($args) {
        $port = 8080;

            if(!empty($args))
            {
                $port = $args[0];
            }

            Console::success("PHP built-in has started at port " . $port . ".");
            exec("php -S localhost:" . $port);
        });
    }

    /**
     * This is where everything happens.
     * 
     * @return  void
     */

    private function runtime()
    {
        if($this->executed && !$this->running)
        {
            $arguments = $this->arguments;

            if(!empty($arguments))
            {
                $directive = strtolower($arguments[0]);
                
                array_shift($arguments);
                error_reporting(0);

                $this->registerDefaultOptions();
                $this->loadCustomOptions($arguments);

                if($this->matchArguments($directive))
                {
                    $this->getOption()->execute($arguments);
                    $this->terminate();
                }
            }
            
            Console::error("Unknown atmos command.");
        }

        $this->running = true;
    }

    /**
     * Load all custom options specified from config.
     * 
     * @param   array $arguments
     * @return  void
     */

    private function loadCustomOptions(array $arguments)
    {
        $path = $this->config('directory');

        if(file_exists($path) && is_readable($path))
        {
            $php = array_unique(glob($path . "/*.php"));

            if(!empty($php))
            {
                foreach($php as $file)
                {
                    require $file;

                    $filename   = explode('.', basename($file))[0];
                    $namespace  = $this->config('namespace') . $filename;
                    $keyword = strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^-])([A-Z][a-z])/'], '$1-$2', $filename));
                    $instance   = new $namespace($arguments);
                        
                    $this->register([
                        'id'                => $keyword,
                        'description'       => $instance->getDescription(),
                        'directives'        => [$keyword],
                    ], $instance);
                }
            }
        }
        else
        {
            Console::error("Console directory is missing.");
            $this->terminate();
        }
    }

    /**
     * Just terminate the script.
     * 
     * @return  void
     */

    private function terminate()
    {
        $this->running = true;
        $this->end();
    }

    /**
     * End the runtime and terminate the script.
     * 
     * @return  void
     */

    public function end()
    {
        if($this->running && !$this->ended)
        {
            $this->ended = true;
            exit(0);
        }
    }

    /**
     * Initiate the command line and pass the
     * arguments from the user terminal.
     * 
     * @param   array $argv
     * @param   array $config
     * @return  \Atmos\CommandLine
     */

    public static function init(array $argv, array $config)
    {
        if(is_null(static::$instance))
        {
            static::$instance = new self($argv, $config);
        }

        return static::$instance;
    }

    /**
     * Return the current command line instance.
     * 
     * @return  \Atmos\CommandLine
     */

    public static function context()
    {
        return static::$instance;
    }

}