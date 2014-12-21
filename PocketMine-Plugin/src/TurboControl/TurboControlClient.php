<?php

namespace TurboControl;

class TurboControlClient{
	const C_PASS = 0x0000;
	const C_DISCONN = 0x0001;
	const C_PING = 0x0002;
	const C_PONG = 0x0003;

	const S_PASS = 0x8000;
	const S_DISCONN = 0x8001;
	const S_PING = 0x8002;
	const S_PONG = 0x8003;
	/** @var float */
	private $connectTime;
	private $lastPong;
	/** @var TurboControlServer */
	private $server;
	/** @var SocketStream */
	private $stream;
	/** @var string */
	private $ip;
	/** @var int */
	private $port;
	/** @var bool */
	private $passed = false;
	public function __construct(TurboControlServer $server, $sk){
		$this->lastPong = $this->connectTime = microtime(true);
		$this->server = $server;
		$this->stream = new SocketStream($sk);
		socket_getpeername($sk, $this->ip, $this->port);
	}
	public function getId(){
		return "$this->ip:$this->port";
	}
	public function getIp(){
		return $this->ip;
	}
	public function getPort(){
		return $this->port;
	}
	public function disconnect($reason){
		$pk = new TurboControlOutPacket(self::S_DISCONN);
		$pk->writeString($reason);
		$this->server->unsetClient($this);
		$this->stream->close();
	}
	public function tcThreadTick(){
		while(($pk = $this->stream->receivePacket()) !== null){
			$this->handlePacket($pk);
		}
		$micro = microtime(true);
		if(!$this->passed and $micro - $this->connectTime > $this->server->getPassTimeout()){
			$this->disconnect("No C_PASS sent in {$this->server->getPassTimeout()} second(s)");
			return;
		}
		if($micro - $this->lastPong > $this->server->getPingTimeout()){
			$this->disconnect("No C_PONG packet received in {$this->server->getPingTimeout()} second(s)");
			return;
		}
	}
	private function handlePacket(TurboControlInPacket $pk){
		switch($pk->getPid()){
			case self::C_PASS:
				$password = $pk->readString();
				if($password === $this->server->getPassword()){
					$this->passed = true;
					$opk = new TurboControlOutPacket(self::S_PASS);
					$this->stream->sendPacket($opk);
				}
				else{
					$this->disconnect("Wrong password");
				}
				break;
			case self::C_DISCONN:
				$reason = $pk->readString();
				$this->server->unsetClient($this, "Client disconnect: $reason");
				$this->stream->close();
				break;
			case self::C_PING:
				$this->stream->sendPacket(new TurboControlOutPacket(self::S_PONG));
				break;
			case self::C_PONG:
				$this->lastPong = microtime(true);
				break;
		}
	}
	/**
	 * @return bool
	 */
	public function isPassed(){
		return $this->passed;
	}
	public function checkTimeout(){

	}
}
