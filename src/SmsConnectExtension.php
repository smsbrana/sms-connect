<?php

namespace Neogate\SmsConnect;

use Nette\DI\CompilerExtension;


class SmsConnectExtension extends CompilerExtension
{

	/**
	 * @override
	 */
	public function loadConfiguration()
	{
		$config = $this->getConfig();
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('smsconnect'))
			->setFactory(SmsConnect::class, [
				$config['login'],
				$config['password'],
			]);
	}

}
