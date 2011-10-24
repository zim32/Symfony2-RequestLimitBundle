<?php

namespace Zim32\RequestLimitBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use \Symfony\Component\DependencyInjection\ContainerBuilder;
use \Symfony\Component\DependencyInjection\Compiler\PassConfig;
use \Zim32\RequestLimitBundle\DependencyInjection\Compiler\RegisterKernelListenersPass;

class Zim32RequestLimitBundle extends Bundle
{
	public function build(ContainerBuilder $container){
		$container->addCompilerPass(new RegisterKernelListenersPass(), PassConfig::TYPE_AFTER_REMOVING);
	}
}
