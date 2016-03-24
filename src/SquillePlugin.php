<?php

namespace Burdz\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Plugin\PluginInterface;
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
    const EXTRA_KEY = 'squille-plugin';

    /**
     * @var \Composer\Composer
     */
    protected $composer;

    /**
     * @var \Composer\IO\IOInterface
     */
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
            ScriptEvents::POST_INSTALL_CMD => 'onPostInstallOrUpdate',
            ScriptEvents::POST_UPDATE_CMD  => 'onPostInstallOrUpdate',
        ];
    }

    /**
     * Aggregate all Squille plugin declaration to an
     * unique declaration file after a composer install or update
     *
     * @return void
     */
    public function onPostInstallOrUpdate()
    {
        $plugins = [];
        $extra = $this->composer->getPackage()->getExtra();
        if (isset($extra[self::EXTRA_KEY])) {
            $plugins += $extra[self::EXTRA_KEY];
        }
        $packages = $this->composer->getRepositoryManager()->getLocalRepository()->getPackages();
        foreach ($packages as $package) {
            $extra = $package->getExtra();
            if (isset($extra[self::EXTRA_KEY])) {
                $plugins += $extra[self::EXTRA_KEY];
            }
        }
        $this->dump($plugins);
        if ($this->inputOutput->isVeryVerbose()) {
            array_walk($plugins, function ($pluging) {
                $message = "<info>[Squille pluging]</info> {$pluging}";
                $this->io->write($message);
            });
        }
    }

    /**
     * Create a class who can return plugin list
     * and add it to autoload files
     *
     * @param array $plugins
     */
    protected function dump(array $plugins)
    {
        $class = '<?php
            namespace Burdz\Composer;
            class PluginList
            {
                public static function getAll()
                {
                    return '.var_export($plugins, true).';
                }
            }
        ';
        $file = __DIR__.'/PluginList.php';
        file_put_contents($file, $class);
        $this->composer->getPackage()->setAutoload(['files' => [$file]]);
    }
}
