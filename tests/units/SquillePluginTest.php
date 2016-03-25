<?php

namespace Burdz\Squille\Test;

use Burdz\Squille\Composer\SquillePlugin;
use Composer\Script\ScriptEvents;

class SquillePluginTest extends \PHPUnit_Framework_TestCase
{
    protected $plugin;

    protected $composer;

    protected function setUp()
    {
        parent::setUp();
        $this->composer = $this->getMock('Composer\\Composer');
        $rootPackage = $this->getMockBuilder('\Composer\Package\RootPackageInterface')->getMockForAbstractClass();
        $this->composer->expects($this->any())->method('getPackage')->willReturn($rootPackage);
        $io = $this->getMockBuilder('Composer\\IO\\IOInterface')->getMockForAbstractClass();
        $this->plugin = new SquillePlugin();
        $this->plugin->activate($this->composer, $io);
    }

    public function testSubscribedRightEvents()
    {
        $events = SquillePlugin::getSubscribedEvents();
        $this->assertArrayHasKey(ScriptEvents::PRE_AUTOLOAD_DUMP, $events);
        $this->assertEquals('generatePluginFile', $events[ScriptEvents::PRE_AUTOLOAD_DUMP]);
    }

    public function testDumpFileExists()
    {
        $file = __DIR__.'/../../vendor/test-squille-plugin.php';
        $this->plugin->dump($file, ['Foo\\Bar\\Baz']);
        $this->assertFileExists($file);
    }
}