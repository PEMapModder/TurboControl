package org.zapto.pemapmodder.turbocotrol.org.zapto.pemapmodder.turbocontrol.lib;

import java.io.DataOutputStream;
import java.io.IOException;
import java.nio.ByteBuffer;
import java.nio.ByteOrder;

public class TurboControlOutPacket{
	private ByteBuffer bb;
	public TurboControlOutPacket(short pid, int contentSize){
		bb = ByteBuffer.allocate(contentSize + 2).order(ByteOrder.BIG_ENDIAN);
		bb.putShort(pid);
	}
	public void putString(String string){
		bb.putShort((short) string.length());
		bb.put(string.getBytes());
	}
	public ByteBuffer bb(){
		return bb;
	}
	public void writePacket(DataOutputStream os) throws IOException{
		byte[] array = bb.array();
		int p = 0;
		while(true){
			if(p + 0x7FFF >= array.length){ // if this is the last slice
				short length = (short) (array.length - p);
				os.writeShort(length);
				break;
			}
			os.writeShort(0x8000); // has next
			os.write(array, p, 0x7FFF);
			p += 0x7FFF;
		}
	}
}
