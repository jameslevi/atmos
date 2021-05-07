<?php

namespace Graphite\Component\Atmos;

use Graphite\Component\Stencil\Comment;
use Graphite\Component\Stencil\Method;
use Graphite\Component\Stencil\Stencil;

class CommandLine extends OptionManager
{
    /**
     * Current atmos version.
     * 
     * @var string
     */
    private $version = '1.0.4';

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

        // Register command for returning current atmos version.
        $this->register(array(
            'id'            => 'version',
            'description'   => 'Return the current version of atmos.',
            'directives'    => ['-v', '--version'],
        ), function($args) use ($that) {

            Console::log("current version \e[33m" . $that->version);

        });

        // Register command for clearing screen. 
        $this->register(array(
            'id'            => 'clear',
            'description'   => 'Clear the terminal screen.',
            'directives'    => ['-x', '--clear'],
        ), function($args) {

            print("\033[2J\033[;H");

        });

        // Register command for listing available options.
        $this->register(array(
            'id'            => 'help',
            'description'   => 'Return list of all built-in options.',
            'directives'    => ['-h', '--help'],
        ), function($args) use ($that) {

            Console::log("Atmos \e[33mversion " . $that->version);
            Console::log("Is a simple library for creating command line scripts in PHP.");
            Console::lineBreak();
            Console::warn("Usage:");
            Console::log("    php atmos [directive] [param1] [param2] ...");
            Console::lineBreak();
            Console::warn("Options:");

            $that->listAllOptions();
            Console::lineBreak();

        });

        // Register command for generating new PHP class.
        $this->register(array(
            'id'            => 'make',
            'description'   => 'Generate a new PHP command class file.',
            'directives'    => ['-m', '--make'],
        ), function($args) use ($that) {

            if(!empty($args))
            {
                if(str_count_special_chars($args[0]) == 0)
                {
                    $file = ucfirst($args[0]);
                    $path = $that->config('directory') . '/' . $file . '.php';

                    if(!file_exists($path))
                    {
                        $template = new Stencil($file);
                        $template->setNamespace($that->config('namespace'));
                        $template->use("Graphite\Component\Atmos\Command");
                        $template->extends("Command");
                        $template->setIndention(1);
                        $template->addComment(Comment::makeStringVar("Something that will describe your command class."));
                        $template->addProtectedVariable("description", "No available description...");
                        $template->lineBreak();
                        $template->addComment(Comment::makeMethod("Default method to call if nothing indicated.")->addArrayParam("arguments"));
                        $template->addMethod(Method::makeProtected("main")->addArrayParam("arguments"));
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

        // Register command for starting PHP built-in server.
        $this->register(array(
            'id'            => 'serve',
            'description'   => 'Start the built-in PHP server.',
            'directives'    => ['-s', '--serve'],
        ), function($args) {
        $port = 8080;

            if(!empty($args) && is_numeric($args[0]))
            {
                $port = $args[0];
            }

            Console::success("PHP built-in has started at port " . $port . ".");
            exec("php -S localhost:$port");
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
                $explode         = explode(":", strtolower($arguments[0]));
                $directive       = $explode[0];
                $method          = $explode[1] ?? null;
                
                array_shift($arguments);
                error_reporting(0);

                $this->registerDefaultOptions();
                $this->loadCustomOptions($arguments);

                if($this->matchArguments($directive))
                {
                    $this->getOption()->execute($arguments, $method);
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
                    require_once $file;

                    $filename       = explode('.', basename($file))[0];
                    $namespace      = $this->config('namespace') . '\\' . $filename;
                    $keyword        = str_camel_to_kebab($filename);
                    $instance       = new $namespace($arguments);
                    $alias          = $instance->getAlias();
                    $aliases        = array($keyword);

                    if(!is_null($alias))
                    {
                        $aliases[] = $alias;
                    }
                        
                    $this->register(array(
                        'id'                => $keyword,
                        'description'       => $instance->getDescription(),
                        'directives'        => $aliases,
                    ), $instance);
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
     * @return  $this
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
     * @return  $this
     */
    public static function context()
    {
        return static::$instance;
    }

}