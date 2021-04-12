# ATMOS CLI

![](https://img.shields.io/badge/packagist-v1.0.1-informational?style=flat&logo=<LOGO_NAME>&logoColor=white&color=2bbc8a) ![](https://img.shields.io/badge/license-MIT-informational?style=flat&logo=<LOGO_NAME>&logoColor=white&color=2bbc8a)

Is a simple library for creating command line scripts in PHP.

# Installation
1. You can install via composer using *"composer require jameslevi/atmos"*.
2. Copy the atmos file from vendor/jameslevi/atmos to root directory.
3. Open the atmos file and set the directory where to save command files.
```php
$atmos = \Atmos\CommandLine::init($argv);

// Set the atmos configurations.
$atmos->setConfig([
    'directory'         => __DIR__ . '\\folder',
]);

// Execute the command line scripts.
$atmos->exec();

// End and terminate the script.
$atmos->end();
```
4. List all available commands using *"php atmos --help"*.  

# Getting Started  
1. Create a new command file using *"php atmos --make filename"*.
2. Open the generated PHP command file and write your code inside the execute method.
```php
/**
 * Method to be executed in the command line.
 *
 * @param  array $arguments
 * @return void
 */

protected function execute(array $arguments)
{
    Console::log("Hello World!");  
}
```  
3. Test the command using *"php atmos filename"*.

# Console Messages
1. **Log** - Print a simple message.
```php
Console::log("Hello World!");
```
2. **Success** - Print a success message.
```php
Console::success("Congratulations! you made it!");
```
3. **Error** - Print an error message.
```php
Console::error("Something went wrong!");
```
4. **Info** - Print an info message.
```php
Console::info("You scored 30 points!");
```
5. **Warning** - Print a warning message.
```php
Console::warn("I told you not to go here!");
```

# Call Multiple Commands
Very useful if you want to call multiple commands in just a single command. The order of execution of each command depends on the order of values in array.
```php
Console::call([
    'composer -h',
    'php atmos -h'
]);
```
# Start Built-in PHP Server
You can now start PHP server using atmos commands. This command will start the server at port 8080.
```
php atmos --serve 8080
```

# Contribution
For issues, concerns and suggestions, you can email James Crisostomo via nerdlabenterprise@gmail.com.

# License  
This package is an open-sourced software licensed under [MIT](https://opensource.org/licenses/MIT) License.
