<?php

namespace TurboControl;

use pocketmine\Thread;

class TurboControlServer extends Thread{
	/** @var resource */
	private $sk;
	/** @var bool */
	private $running = false;
	/** @var TurboControlClient[] */
	private $clients = [];
	/** @var TurboControl */
	private $plugin;
	/** @var string */
	private $ip;
	/** @var int */
	private $port;
	/** @var string */
	private $password;
	/** @var number */
	private $pingTimeout;
	/** @var number */
	private $passTimeout;
	public function __construct(TurboControl $plugin){
		/** @var string $ip */
		/** @var int $port */
		/** @var string $password */
		/** @var number $pingTimeout */
		/** @var number $passTimeout */
		extract($plugin->getConfig()->getAll());
		if(!($this->sk = socket_create(AF_INET, SOCK_STREAM, SOL_TCP))){
			throw new \RuntimeException("Cannot create socket");
		}
		if(!socket_bind($this->sk, $ip, $port)){
			throw new \RuntimeException("Cannot bind socket to $ip:$port");
		}
		if(!socket_listen($this->sk, 5)){
			throw new \RuntimeException("Cannot listen to socket");
		}
		socket_set_nonblock($this->sk);
		$this->plugin = $plugin;
		$this->ip = $ip;
		$this->port = $port;
		$this->password = $password;
		$this->pingTimeout = $pingTimeout;
		$this->passTimeout = $passTimeout;
	}
	public function run(){
		$this->running = true;
		while($this->running){
			while(is_resource($sk = socket_accept($this->sk))){
				$client = new TurboControlClient($this, $sk);
				$this->clients[$client->getId()] = $client;
			}
			foreach($this->clients as $client){
				$client->tcThreadTick();
			}
		}
	}
	public function unsetClient(TurboControlClient $client){
		unset($this->clients[$client->getId()]);
	}
	public function stop(){
		$this->running = false;
		$this->join();
		socket_close($this->sk);
	}
	public function __destruct(){
		@socket_close($this->sk);
	}
	/**
	 * @return string
	 */
	public function getPassword(){
		return $this->password;
	}
	/**
	 * @return TurboControl
	 */
	public function getPlugin(){
		return $this->plugin;
	}
	/**
	 * @return string
	 */
	public function getIp(){
		return $this->ip;
	}
	/**
	 * @return int
	 */
	public function getPort(){
		return $this->port;
	}
	/**
	 * @return number
	 */
	public function getPingTimeout(){
		return $this->pingTimeout;
	}
	/**
	 * @return number
	 */
	public function getPassTimeout(){
		return $this->passTimeout;
	}
}
