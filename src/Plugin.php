<?php
declare(strict_types=1);

namespace CakePHPTightrope;

use Cake\Console\CommandCollection;
use Cake\Core\BasePlugin;
use CakePHPTightrope\Command\ModifyErrorCommand;

/**
 * Plugin for CakePHPTightrope
 */
class Plugin extends BasePlugin
{
    public function console(CommandCollection $commands): CommandCollection
    {
        return $commands
            ->add('tightrope install', ModifyErrorCommand::class);
    }
}
