<?php

namespace Parabol\BaseBundle\Composer;

use Composer\Script\Event;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Parabol\BaseBundle\Component\Console\SymfonyStyle;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
* 
*/
class BaseScriptHandler
{
    protected static $kernel;

    protected static $options = [
        'symfony-bin-dir' => 'bin',
        // 'bundles' => [], todo: add additional bundles from composer
    ];

    protected static $bundles = null;
    protected static $skeletons = null;
    protected static $appParameters = null;

    public static function getBundles()
    {
        return static::$bundles;
    }

    public static function getSkeletons()
    {
        return static::$skeletons;
    }

    public static function getAppParameters()
    {
        return static::$appParameters;
    }


    protected static function mergeArrayDuplicate($arr)
    {
        foreach($arr as $key => $value)
        {
            if(is_array($value)) $arr[$key] = static::mergeArrayDuplicate($value);
            else $arr = array_unique($arr);
        }

        return $arr;
    }

    protected static $io;


    protected static function getOptions(Event $event)
    {
        

        $options = array_merge(static::$options, $event->getComposer()->getPackage()->getExtra());

        $options['process-timeout'] = $event->getComposer()->getConfig()->get('process-timeout');
        $options['vendor-dir'] = $event->getComposer()->getConfig()->get('vendor-dir');

        static::initKernel($options);
        $options['project-dir'] = substr(static::$kernel->getRootDir(),0,-3);


        return $options;
    }

    protected static function getIO()
    {
        if(!static::$io)
        {
            $input = new ArrayInput([]);
            $output = new ConsoleOutput(OutputInterface::VERBOSITY_NORMAL, true);
            static::$io = new SymfonyStyle($input, $output);
        }
        return static::$io;
    }



    protected static function executeCommand($consoleDir, $class, $cmd, $arguments, $timeout = 300)
    {
        
        $command = new $class($cmd);
        $command->setApplication(new Application(static::$kernel));
        if($command instanceof \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand) $command->setContainer(static::$kernel->getContainer());

        $input = new ArrayInput($arguments);
        $output = new ConsoleOutput( OutputInterface::VERBOSITY_NORMAL, true );
        $command->run($input, $output);

    }

    private static function initKernel($options)
    {
        if(!static::$kernel)
        {
            require $options['vendor-dir'] . '/autoload.php';

            static::$kernel = new \AppKernel('dev', true);
            static::$kernel->boot();
        }
    }
}