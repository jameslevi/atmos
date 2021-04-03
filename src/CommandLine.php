<?php

namespace Atmos;

class CommandLine extends OptionManager
{
    /**
     * Current atmos version.
     * 
     * @var string
     */

    private $version = '1.0.0';

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
     * @return  void
     */

    private function __construct(array $argv, array $config = null)
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
     * Set the configuration data.
     * 
     * @param   array $config
     * @return  \Atmos\CommandLine
     */

    public function setConfig(array $config)
    {
        $this->config = $config;

        return $this;
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

        // Register a command that will return current version.
        $this->register(['-v', '--version'], 'Return the current version of ATMOS CLI.', function($arguments) {

            Console::log("ATMOS CLI \e[33mv" . $this->version);

        });

        // Register a command that will clear the terminal.
        $this->register(['-c', '--clear'], 'Clear the current terminal.', function($arguments) {

            print("\033[2J\033[;H");

        });

        // Register a command that will list all registered commands.
        $this->register(['-h', '--help'], 'Return the list of all registered options.', function($arguments) use ($that) {

            Console::log("ATMOS CLI \e[33mv" . $this->version);
            Console::log("Is a simple library for creating command line scripts in PHP.");
            Console::lineBreak();
            Console::warn("Usage:");
            Console::log("    php atmos [directive] [param1] [param2] ...");
            Console::lineBreak();
            Console::warn("Options:");

            $that->listAllOptions();
            Console::lineBreak();

        });

        // Register a command that will generate new command file.
        $this->register(['-m', '--make'], 'Create a new command file.', function($arguments) use ($that) {

            if(!empty($arguments))
            {
                $path = $that->config('directory') . '/' . ucfirst($arguments[0]) . '.php';

                if(!file_exists($path))
                {
                    $template = "<?php";
                    $template .= PHP_EOL;
                    $template .= PHP_EOL;
                    $template .= "namespace Atmos\Console;";
                    $template .= PHP_EOL;
                    $template .= PHP_EOL;
                    $template .= "use Atmos\CLI;";
                    $template .= PHP_EOL;
                    $template .= PHP_EOL;
                    $template .= "class " . ucfirst($arguments[0]) . " extends CLI";
                    $template .= PHP_EOL;
                    $template .= "{";
                    $template .= PHP_EOL;
                    $template .= '    /**';
                    $template .= PHP_EOL;
                    $template .= '     * List of directives that will execute this command.';
                    $template .= PHP_EOL;
                    $template .= '     *';
                    $template .= PHP_EOL;
                    $template .= '     * @var array';
                    $template .= PHP_EOL;
                    $template .= '     */';
                    $template .= PHP_EOL;
                    $template .= PHP_EOL;
                    $template .= '    protected $directives = array();';
                    $template .= PHP_EOL;
                    $template .= PHP_EOL;
                    $template .= '    /**';
                    $template .= PHP_EOL;
                    $template .= '     * Description of this command.';
                    $template .= PHP_EOL;
                    $template .= '     *';
                    $template .= PHP_EOL;
                    $template .= '     * @var string';
                    $template .= PHP_EOL;
                    $template .= '     */';
                    $template .= PHP_EOL;
                    $template .= PHP_EOL;
                    $template .= '    protected $description = "No available description...";';
                    $template .= PHP_EOL;
                    $template .= PHP_EOL;
                    $template .= '    /**';
                    $template .= PHP_EOL;
                    $template .= '     * Method to be executed in the command line.';
                    $template .= PHP_EOL;
                    $template .= '     *';
                    $template .= PHP_EOL;
                    $template .= '     * @param  array $arguments';
                    $template .= PHP_EOL;
                    $template .= '     * @return void';
                    $template .= PHP_EOL;
                    $template .= '     */';
                    $template .= PHP_EOL;
                    $template .= PHP_EOL;
                    $template .= '    protected function execute(array $arguments)';
                    $template .= PHP_EOL;
                    $template .= "    {";
                    $template .= PHP_EOL;
                    $template .= PHP_EOL;
                    $template .= "    }";
                    $template .= PHP_EOL;
                    $template .= PHP_EOL;
                    $template .= "}";

                    $file = fopen($path, 'w');
                    fwrite($file, $template);
                    fclose($file);

                    Console::success("New console file was successfully created.");
                }
                else
                {
                    Console::error("Command file already exists.");
                }
            }
            else
            {
                Console::error("Please enter filename to create new command file.");
            }
        });

        // Register a command that will return configuration value.
        $this->register(['--config'], 'Return configuration values.', function($arguments) use ($that) {

            if(!empty($arguments))
            {
                $config = $that->config();
                $key = $arguments[0];

                if(array_key_exists($key, $config))
                {
                    Console::info(ucfirst($key) . ": " . $config[$key]);
                }
                else
                {
                    Console::error("Undefined configuration key.");
                }
            }
            else
            {
                Console::error("Missing configuration key.");
            }
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

        if(file_exists($path))
        {
            $php = array_unique(glob($path . "/*.php"));

            if(!empty($php))
            {
                foreach($php as $file)
                {
                    if(file_exists($file) && is_readable($file))
                    {
                        require $file;

                        $filename   = explode('.', basename($file))[0];
                        $namespace  = "Atmos\Console\\" . $filename;
                        $instance   = new $namespace($arguments);

                        $this->register($instance->getDirectives(), $instance->getDescription(), $instance);
                    }
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
     * @return  \Atmos\CommandLine
     */

    public static function init(array $argv)
    {
        if(is_null(static::$instance))
        {
            static::$instance = new self($argv);
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