<?php

namespace Directus\Console\Modules;

use Directus\Console\Exception\UnsupportedCommandException;
use Directus\Console\Exception\WrongArgumentsException;

class ModuleBase implements ModuleInterface
{
    protected $__module_name;
    protected $__module_description;

    protected $commands_help;
    protected $help;

    public function getModuleName()
    {
        return $this->__module_name;
    }

    public function getInfo()
    {
        return $this->__module_name . ': ' . __t($this->__module_description);
    }

    public function getCommands()
    {
        return $this->commands_help;
    }

    public function getCommandHelp($command)
    {
        if (!array_key_exists($command, $this->help)) {
            throw new UnsupportedCommandException($this->__module_name . ':' . $command . __t(' command does not exists!'));
        }
        return $this->help[$command];
    }

    public function runCommand($command, $args, $extra)
    {
        $cmd_name = 'cmd' . ucwords($command);
        if (!method_exists($this, $cmd_name)) {
            throw new UnsupportedCommandException($this->__module_name . ':' . $command . __t(' command does not exists!'));
        }
        $this->$cmd_name($args, $extra);
    }

    public function cmdHelp($args, $extra)
    {
        if (count($extra) == 0) {
            throw new WrongArgumentsException($this->__module_name . ':help ' . __t('missing command to show help for!'));
        }

        echo PHP_EOL . __t('Directus Command ') . $this->__module_name . ':' . $extra[0] . __t(' help') . PHP_EOL . PHP_EOL;
        echo "\t" . $this->commands_help[$extra[0]] . PHP_EOL;
        if(trim($this->getCommandHelp($extra[0]))) {
            echo "\t" . __t('Options: ') . PHP_EOL . $this->getCommandHelp($extra[0]);
        }
        echo PHP_EOL . PHP_EOL;
    }
}
