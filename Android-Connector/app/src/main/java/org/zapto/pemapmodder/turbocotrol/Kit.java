package org.zapto.pemapmodder.turbocotrol;

import android.content.Context;
import android.widget.Toast;

public class Kit{
	private Context ctx;
	public Kit(Context ctx){
		this.ctx = ctx;
	}
	public void e(Throwable t){
		Toast.makeText(ctx, ctx.getString(R.string.kit_e_toast$1)
				.replace("$class$", t.getClass().getCanonicalName().substring(1))
				.replace("$message$", t.getLocalizedMessage()),
				Toast.LENGTH_LONG).show();
		StringBuilder stackTrace = new StringBuilder("Stack trace:\n");
		int k = 0;
		for(StackTraceElement line: t.getStackTrace()){
			stackTrace.append(ctx.getString(R.string.kit_e_toast$2)
					.replace("$k$", Integer.toString(k++))
					.replace("$method$", line.getMethodName())
					.replace("$class$", line.getClassName())
					.replace("$file$", line.getFileName())
					.replace("$line$", Integer.toString(line.getLineNumber()))
			);
		}
		Toast.makeText(ctx, stackTrace.toString(), Toast.LENGTH_LONG).show();
	}
}
