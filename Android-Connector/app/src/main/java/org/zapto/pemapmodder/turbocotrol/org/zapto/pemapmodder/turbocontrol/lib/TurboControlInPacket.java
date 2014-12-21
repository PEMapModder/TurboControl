package org.zapto.pemapmodder.turbocotrol.org.zapto.pemapmodder.turbocontrol.lib;

import java.nio.ByteBuffer;
import java.nio.ByteOrder;

public class TurboControlInPacket{
	private ByteBuffer bb;
	private short pid;
	public TurboControlInPacket(byte[] buffer){
		bb = ByteBuffer.wrap(buffer).order(ByteOrder.BIG_ENDIAN);
		pid = bb.getShort();
	}
	public ByteBuffer bb(){
		return bb;
	}
	public short getPid(){
		return pid;
	}
}
