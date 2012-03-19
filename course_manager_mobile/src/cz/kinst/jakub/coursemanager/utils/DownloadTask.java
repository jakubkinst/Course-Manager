package cz.kinst.jakub.coursemanager.utils;

import java.io.File;

import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.content.Intent;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Environment;
import android.widget.Toast;
import cz.kinst.jakub.coursemanager.CourseManagerConnector;

public class DownloadTask extends AsyncTask<JSONObject, Void, File> {

	private Activity context;
	private CourseManagerConnector cm;

	public DownloadTask(Activity context, CourseManagerConnector cm) {
		this.context = context;
		this.cm = cm;
	}

	protected void onPreExecute() {
		context.setProgressBarIndeterminateVisibility(true);
		Toast.makeText(context, "Download started", 2000).show();
	}

	protected File doInBackground(JSONObject... resource) {
		File myFolder = new File(Environment.getExternalStorageDirectory()
				+ "/CourseManager_downloads");
		myFolder.mkdirs();
		File file = new File("");
		try {
			file = cm.getResource(resource[0].getInt("id"), myFolder + "/"
					+ resource[0].getString("name"));
		} catch (JSONException e) {
		}
		return file;
	}

	protected void onPostExecute(File file) {
		Toast.makeText(context, "File saved to " + file.getAbsolutePath(), 2000)
				.show();
		try {
			Intent intent = new Intent();
			intent.setAction(android.content.Intent.ACTION_VIEW);
			intent.setDataAndType(Uri.fromFile(file), getMIMEType(file));
			context.startActivity(intent);
		} catch (Exception e) {
			Toast.makeText(
					context,
					"No application found to open this file. File was just saved.",
					2000).show();
		}
		context.setProgressBarIndeterminateVisibility(false);

	}

	public String getMIMEType(File f) {
		String filenameArray[] = f.getName().split("\\.");
		String e = filenameArray[filenameArray.length - 1];
		String mime = "";
		e = e.toLowerCase();

		if (e.equals("jpg"))
			mime = "image/jpg";
		if (e.equals("jpeg"))
			mime = "image/jpeg";
		if (e.equals("png"))
			mime = "image/png";
		if (e.equals("gif"))
			mime = "image/gif";
		if (e.equals("mp3"))
			mime = "audio/mp3";
		if (e.equals("html"))
			mime = "text/html";
		if (e.equals("pdf"))
			mime = "application/pdf";
		if (e.equals("doc"))
			mime = "application/doc";
		if (e.equals("apk"))
			mime = "application/vnd.android.package-archive";
		if (e.equals("txt"))
			mime = "text/plain";

		return mime;
	}

}