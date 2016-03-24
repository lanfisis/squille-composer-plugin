<?php

namespace Burdz\Squille\Composer;

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
     * Plugin class file path
     *
     * @var string
     */
    protected $file;

    /**
     * {@inheritdoc}
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io       = $io;
        $this->file     = __DIR__.'/PluginList.php';
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ScriptEvents::POST_INSTALL_CMD => 'onPostInstallOrUpdate',
            ScriptEvents::POST_UPDATE_CMD  => 'onPostInstallOrUpdate',
            ScriptEvents::PRE_INSTALL_CMD => 'onPreInstallOrUpdate',
            ScriptEvents::PRE_UPDATE_CMD  => 'onPreInstallOrUpdate',
        ];
    }

    /**
     * Prepare plugin class file and add it to autoload
     *
     * @return void
     */
    public function onPreInstallOrUpdate()
    {
        $class = '<?php
            namespace Burdz\Squille\Composer;
            class PluginList
            {
                public static function getAll()
                {
                    return [];
                }
            }
        ';
        file_put_contents($this->file, $class);
        $autoload = $this->composer->getPackage()->getAutoload();
        $autoload['files'][] = $this->file;
        $this->composer->getPackage()->setAutoload($autoload);
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

        $content = file_get_contents($this->file);
        file_put_contents($this->file, str_replace('[]', var_export($plugins, true), $content));
        
        if ($this->io->isVerbose()) {
            array_walk($plugins, function ($pluging) {
                $message = "<info>[Squille pluging]</info> {$pluging}";
                $this->io->write($message);
            });
        }
    }
}
