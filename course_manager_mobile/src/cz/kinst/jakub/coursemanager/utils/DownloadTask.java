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
import cz.kinst.jakub.coursemanager.R;

/**
 * Async Task Providing HTTP file download takes resource JSON Object and
 * downloads the file from server
 * 
 * @author Jakub Kinst
 * 
 */
public class DownloadTask extends AsyncTask<JSONObject, Void, File> {

	/**
	 * Location of files saved to device
	 */
	private static final String FILE_DIRECTORY = "CourseManager_downloads";

	/**
	 * Context of an application
	 */
	private Activity context;

	/**
	 * CourseManagerConnector instance
	 */
	private CourseManagerConnector cm;

	/**
	 * Default constructor Takes any activity as context and
	 * CourseManagerConnector instance
	 * 
	 * @param context
	 * @param cm
	 */
	public DownloadTask(Activity context, CourseManagerConnector cm) {
		this.context = context;
		this.cm = cm;
	}

	@Override
	protected void onPreExecute() {
		context.setProgressBarIndeterminateVisibility(true);
		Toast.makeText(context, R.string.download_started, 2000).show();
	}

	@Override
	protected File doInBackground(JSONObject... resource) {
		File myFolder = new File(Environment.getExternalStorageDirectory()
				+ "/" + FILE_DIRECTORY);
		myFolder.mkdirs();
		File file = new File("");
		try {
			// get HTTP response
			file = cm.getResource(resource[0].getInt("id"), myFolder + "/"
					+ resource[0].getString("name"));
		} catch (JSONException e) {
		}
		return file;
	}

	@Override
	/**
	 * After file is downloaded, try to open the file by default application installed
	 */
	protected void onPostExecute(File file) {
		Toast.makeText(context,
				R.string.file_saved_to + file.getAbsolutePath(), 2000).show();
		try {
			Intent intent = new Intent();
			intent.setAction(android.content.Intent.ACTION_VIEW);
			// we need to know file MIME type
			// let's get it from file extension (only well-known extensions)
			intent.setDataAndType(Uri.fromFile(file), Utils.getMIMEType(file));
			context.startActivity(intent);
		} catch (Exception e) {
			Toast.makeText(context, R.string.no_application_found, 2000).show();
		}
		context.setProgressBarIndeterminateVisibility(false);

	}

}