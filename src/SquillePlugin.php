<?php

namespace Burdz\Squille\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\PluginEvents;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;

/**
 * Composer plugin to declare Squille plugins
 *
 * @package   Squille
 * @author    David Buros <david.buros@gmail.com>
 * @copyright 2015 David Buros
 * @licence   WTFPL see LICENCE.md file
 * @see       https://getcomposer.org/doc/articles/plugins.md
 */
class SquillePlugin implements PluginInterface, EventSubscriberInterface
{
    protected $composer;
    protected $io;

    /**
     * {@inheritdoc}
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io       = $io;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            //ScriptEvents::POST_INSTALL_CMD => 'onPostInstallOrUpdate',
            //ScriptEvents::POST_UPDATE_CMD  => 'onPostInstallOrUpdate',
            PluginEvents::COMMAND,
        ];
    }

    /**
     * Aggregate all Squille plugin declaration to an unique declaration file
     * after a composer install or update
     *
     * @param  \Composer\Script\Event $event
     * @return void
     */
    public function onPostInstallOrUpdate(Event $event)
    {
        var_dump('test');
    }
}
