package org.zapto.pemapmodder.turbocotrol.org.zapto.pemapmodder.turbocontrol.lib;

public interface UserInterface{
	public static UserInterface currentInstance = null;
	public void onPassed();
	public void onPingUpdated();
}
