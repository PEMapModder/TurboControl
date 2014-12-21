<?php

namespace TurboControl;

use pocketmine\utils\Binary;

class TurboControlInPacket{
	private $buffer;
	private $pointer;
	private $pid;
	public function __construct($buffer){
		$this->buffer = $buffer;
		if(($this->pid = $this->readShort()) > 0x7FFF){
			throw new \RuntimeException("Illegal packet received: $this->pid (> 0x7FFF)");
		}
	}
	/**
	 * @param $length
	 * @return string
	 */
	public function readRaw($length){
		$result = substr($this->buffer, $this->pointer, $length);
		$this->pointer += $length;
		return $result;
	}
	/**
	 * @return int
	 */
	public function readByte(){
		return ord($this->buffer{$this->pointer++});
	}
	/**
	 * @return int
	 */
	public function readShort(){
		return Binary::readShort($this->readRaw(2));
	}
	/**
	 * @return int
	 */
	public function readInt(){
		return Binary::readInt($this->readRaw(4));
	}
	/**
	 * @return int|string
	 */
	public function readLong(){
		return Binary::readLong($this->readRaw(8));
	}
	/**
	 * @return string
	 */
	public function readString(){
		return $this->readRaw($this->readShort());
	}
	/**
	 * @return int
	 */
	public function getPid(){
		return $this->pid;
	}
}
