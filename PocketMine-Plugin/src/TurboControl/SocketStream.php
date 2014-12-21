<?php

namespace TurboControl;

use pocketmine\utils\Binary;

class SocketStream{
	/** @var resource */
	private $sk;
	public function __construct($sk){
		$this->sk = $sk;
	}
	public function sendPacket(TurboControlOutPacket $pk){
		$splits = str_split($pk->getBuffer(), 0x7FFF); // MTU 0x7FFF
		foreach($splits as $i => $section){
			if($i + 1 !== count($splits)){
				socket_write($this->sk, Binary::writeShort(0x8000)); // if packet is not finished, use 0x8000
			}
			else{
				socket_write($this->sk, Binary::writeShort(strlen($section)));
			}
			socket_write($this->sk, $section);
		}
	}
	public function receivePacket(){
		$buffer = "";
		$length = socket_read($this->sk, 2);
		if($length === false){
			return null;
		}
		read:
		if($length === "\x80\x00"){
			$length = "\x7F\xFF";
			$hasNext = true;
		}
		$buffer .= socket_read($this->sk, Binary::readShort($length));
		if(isset($hasNext) and $hasNext){
			$length = socket_read($this->sk, 2);
			goto read;
		}
		return new TurboControlInPacket($buffer);
	}
	public function close(){
		socket_close($this->sk);
	}
}
