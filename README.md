# Atmos

![](https://img.shields.io/badge/packagist-v1.0.3-informational?style=flat&logo=<LOGO_NAME>&logoColor=white&color=2bbc8a) ![](https://img.shields.io/badge/license-MIT-informational?style=flat&logo=<LOGO_NAME>&logoColor=white&color=2bbc8a)

Is a simple library for creating command line scripts in PHP.

## Features ##  
1. Create good looking PHP command line scripts.
2. Built-in commands to make web development much easier.
3. Easy integration with any PHP frameworks.

## Installation ##  
1. You can install via composer.
```
composer require jameslevi/atmos
```
2. Copy the atmos file from vendor/jameslevi/atmos to root directory.
3. Create a new folder named commands in your root directory.  

## Getting Started  ##  
1. Generate a new command file.
```
php atmos --make Test
```
2. Open the generated PHP command file and write your code inside the main method.
```php
/**
 * Method to be executed in the command line.
 *
 * @param  array $arguments
 * @return void
 */

protected function main(array $arguments)
{
    Console::log("Hello World!");  
}
```  
3. Test the command.
```
php atmos test
```
## Call Specific Methods ##  
1. Add new protected method in your command file. For this example let's say "generate".
```php
/**
 * This method will generate new file.
 *
 * @param  array $arguments
 * @return void
 */

protected function generate(array $arguments)
{
    Console::success("File is generated.");  
}
```
2. Call this method using this command.
```
php atmos test:generate
```
## Arguments ##
1. You can use parameters supplied from the command line.
```php
/**
 * This method will generate new file.
 *
 * @param  array $arguments
 * @return void
 */

protected function generate(array $arguments)
{
    Console::success($arguments[0] . " file is generated.");  
}
```
2. You can call this method using this command.
```php
php atmos test:generate newfile.php
```

## Console Messages ##
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

## Call Multiple Commands ##  
Very useful if you want to call multiple commands in just a single command. The order of execution of each command depends on the order of values in array.
```php
Console::call([
    'composer -h',
    'php atmos -h'
]);
```
## Start Built-in PHP Server ##
You can now start PHP server using atmos commands. This command will start the server at port 8080.
```
php atmos --serve 8080
```

## Contribution ##  
For issues, concerns and suggestions, you can email James Crisostomo via nerdlabenterprise@gmail.com.

## License ##
This package is an open-sourced software licensed under [MIT](https://opensource.org/licenses/MIT) License.
