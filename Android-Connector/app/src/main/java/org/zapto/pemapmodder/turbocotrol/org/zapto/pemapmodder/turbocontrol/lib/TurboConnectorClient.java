package org.zapto.pemapmodder.turbocotrol.org.zapto.pemapmodder.turbocontrol.lib;

import android.os.Bundle;

import org.zapto.pemapmodder.turbocotrol.Kit;

import java.io.IOException;
import java.net.SocketAddress;

public class TurboConnectorClient extends Thread implements TurboControlProtocolInfo{
	private Kit kit;
	private Bundle config;
	private TurboControlSocket socket;
	private boolean running = false;
	private long lastPing;
	private long pingMillis = 0L;
	public TurboConnectorClient(Kit kit, Bundle config, SocketAddress addr) throws IOException{
		this.kit = kit;
		this.config = config;
		socket = new TurboControlSocket(addr);
	}
	@Override
	public void run(){
		running = true;
		try{
			String password = config.getString("password");
			TurboControlOutPacket passPk = new TurboControlOutPacket(C_PASS, 2 + password.length());
			passPk.putString(password);
			socket.sendPacket(passPk);
			TurboControlOutPacket ping = new TurboControlOutPacket(C_PING, 0);
			socket.sendPacket(ping);
			lastPing = System.currentTimeMillis();
			TurboControlInPacket inPk;
			while((inPk = socket.readPacket()) != null){
				handlePacket(inPk);
			}
		}
		catch(IOException e){
			kit.e(e);
		}
		finally{
			try{
				socket.close();
			}
			catch(IOException e){
				kit.e(e);
			}
		}
	}
	public boolean isRunning(){
		return running;
	}
	public void handlePacket(TurboControlInPacket pk) throws IOException{
		switch(pk.getPid()){
			case S_PASS:
				if(UserInterface.currentInstance != null){
					UserInterface.currentInstance.onPassed();
				}
				break; // TODO
			case S_PING:
				TurboControlOutPacket pong = new TurboControlOutPacket(C_PONG, 0);
				socket.sendPacket(pong);
				break;
			case S_PONG:
				pingMillis = System.currentTimeMillis() - lastPing;
				if(UserInterface.currentInstance != null){
					UserInterface.currentInstance.onPingUpdated();
				}
				break;
		}
	}
}
