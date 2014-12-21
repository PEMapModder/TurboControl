package org.zapto.pemapmodder.turbocotrol.org.zapto.pemapmodder.turbocontrol.lib;

public interface TurboControlProtocolInfo{
	public final static short C_PASS = 0x0000;
	public final static short C_DISCONN = 0x0001;
	public final static short C_PING = 0x0002;
	public final static short C_PONG = 0x0003;

	public final static short S_PASS = (short) 0x8000;
	public final static short S_DISCONN = (short) 0x8001;
	public final static short S_PING = (short) 0x8002;
	public final static short S_PONG = (short) 0x8003;
}
