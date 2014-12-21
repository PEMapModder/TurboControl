package org.zapto.pemapmodder.turbocotrol.org.zapto.pemapmodder.turbocontrol.lib;


import java.io.DataInputStream;
import java.io.DataOutputStream;
import java.io.IOException;
import java.net.Socket;
import java.net.SocketAddress;
import java.util.ArrayList;
import java.util.List;

public class TurboControlSocket{
	private Socket socket;
	private DataInputStream is;
	private DataOutputStream os;
	public TurboControlSocket(SocketAddress addr) throws IOException{
		socket = new Socket();
		socket.connect(addr);
		is = new DataInputStream(socket.getInputStream());
		os = new DataOutputStream(socket.getOutputStream());
	}
	public void sendPacket(TurboControlOutPacket pk) throws IOException{
		pk.writePacket(os);
	}
	public TurboControlInPacket readPacket() throws IOException{
		List<byte[]> bufferList = new ArrayList<>();
		short length = is.readShort();
		while(length == 0x8000){
			byte[] subBuffer = new byte[0x7FFF];
			is.read(subBuffer);
			bufferList.add(subBuffer);
			length = is.readShort();
		}
		byte[] lastSub = new byte[length];
		is.read(lastSub);
		byte[] buffer = new byte[bufferList.size() * 0x7FFF + length];
		int i = 0;
		for(byte[] sub : bufferList){
			System.arraycopy(sub, 0, buffer, (i++) * 0x7FFF, 0x7FFF);
		}
		System.arraycopy(lastSub, 0, buffer, i * 0x7FFF, length);
		return new TurboControlInPacket(buffer);
	}
	public void close() throws IOException{
		socket.close();
	}
}
