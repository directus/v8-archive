<?php

namespace Directus\Console\Modules;

interface ModuleInterface
{
    /**
     *  Get the name of the module.
     *   *.
     *
     * @return string the name this module should be known for
     */
    public function getModuleName();

    /**
     *  Get information about a CLI module.
     *
     *  Provides information about a module fit to create a brief description of
     *  the module to be presented to the user.
     *
     * @return string a brief description of the module
     */
    public function getInfo();

    /**
     *  Returns a list of commands provided by the module.
     *
     *  Returns a list of commands provided by the module and a brief usage and
     *  description of the command arguments.  Commands are returned as an
     *  associative array that has the command as the key and a descriptive text
     *  as the value.
     *
     *  <code>
     *  $commands = array(
     *    'config' => 'Configure Directus.',
     *    'database' => 'Populate the DB with the schema.',
     *    'install' => 'Install the initial configuration.'
     *  );
     *  </code>
     *
     * @return array[string]string A brief description of the module commands
     */
    public function getCommands();

    /**
     *  Returns help for a command provided by this module.
     *
     *  Provides help text for a specific command implemented by this module.
     *
     * @param string $command the command to get help for
     *
     * @throws UnsupportedCommand thrown when a module does not support a command
     *
     * @return string a brief description of the module command
     */
    public function getCommandHelp($command);

    /**
     *  Executed a command provided by this module.
     *
     *  Executes a command provided by this module with the arguments passed to the
     *  function.
     *
     *  Arguments are passed already parsed to the command in an associative array
     *  where the key is the name of the argument:
     *
     *  <code>
     *  $args = array(
     *    'db_name' => 'directus',
     *    'db_user' => 'directus_user'
     *  );
     *  </code>
     *
     *  Extra - unamed - arguments are passed in a simple array of strings.
     *
     * @param string               $command the command to execute
     * @param array [string]string $args    The arguments for the command
     * @param array [string]       $extra   Un-named arguments passed to the command
     *
     * @throws UnsupportedCommand if the module does not support a command
     * @throws WrongArguments     if the arguments passed to the command are not
     *                            sufficient or correct to execute the command
     * @throws CommandFailed      if the module failed to execute a command
     */
    public function runCommand($command, $args, $extra);

    /**
     * Returns the given command options.
     *
     * @param string $command
     *
     * @return array
     */
    public function getOptions($command);
}
