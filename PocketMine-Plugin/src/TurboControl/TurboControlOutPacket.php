<?php

namespace TurboControl;

use pocketmine\utils\Binary;

class TurboControlOutPacket{
	private $buffer;
	public function __construct($id){
		$this->buffer = chr($id);
	}
	public function writeRaw($array){
		$this->buffer .= $array;
	}
	public function writeByte($byte){
		if(is_int($byte)){
			$byte = chr($byte);
		}
		$this->buffer .= $byte;
	}
	public function writeShort($short){
		$this->buffer .= Binary::writeShort($short);
	}
	public function writeInt($int){
		$this->buffer .= Binary::writeInt($int);
	}
	public function writeLong($long){
		$this->buffer .= Binary::writeLong($long);
	}
	public function writeString($string){
		$this->writeShort(strlen($string));
		$this->buffer .= $string;
	}
	/**
	 * @return int
	 */
	public function getLength(){
		return strlen($this->buffer);
	}
	/**
	 * @return string
	 */
	public function getBuffer(){
		return $this->buffer;
	}
}
