<?php

namespace TurboControl;

use pocketmine\plugin\PluginBase;

class TurboControl extends PluginBase{
	public function onEnable(){
		$this->saveDefaultConfig();
		$ip = $this->getConfig()->get("ip");
		$port = $this->getConfig()->get("port");
	}
}
