<?php

namespace Parabol\BaseBundle\Util;

class PathUtil
{
	private $container;

	public function __construct($container)
	{
		$this->container = $container;
	}

	public static function generateRouteName($class, $prefix = null)
	{
		return $prefix . strtr(strtolower(preg_replace('#([A-Z])#','_$1',$class)), array('\\' => '','_entity' => ''));
	}

	public static function slugize($sluggableText, $delimiter = '-')
	{
		$urlized = strtolower( trim( preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', iconv('UTF-8', 'ASCII//TRANSLIT', $sluggableText) ), $delimiter) );
        $urlized = preg_replace("/[\/_|+ -]+/", $delimiter, $urlized);
        return $urlized;
	}

	public function getWebDir()
    {
        return $this->container->getParameter('kernel.root_dir').DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.$this->container->getParameter('web_dir');
    }
    
    public function getAbsoluteUploadDir($class, $suffix = '')
    {
        return $this->getWebDir().$this->getUploadDir($class, $suffix);
    }

    public function getUploadDir($class, $suffix = '')
    {
        return $this->container->getParameter('upload_dir').DIRECTORY_SEPARATOR.strtr(strtolower($class), array('\entity' => '', '\\' => '-')).$suffix;
    }

}