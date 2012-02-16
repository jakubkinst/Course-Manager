package cz.kinst.jakub.coursemanager;

import java.util.ArrayList;
import java.util.logging.Level;
import java.util.logging.Logger;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.app.ListActivity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.AsyncTask;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.Window;
import android.widget.ArrayAdapter;

public class CMActivity extends Activity {

	CourseManagerConnector cm;
	boolean isLoading = false;

	@Override
	public void onCreate(Bundle savedInstanceState) {
		
		super.onCreate(savedInstanceState);
		
		
        requestWindowFeature(Window.FEATURE_INDETERMINATE_PROGRESS);
        
		SharedPreferences prefs = PreferenceManager
				.getDefaultSharedPreferences(this);
		if (!prefs.contains("server"))
			prefs.edit().putString("server", "").commit();
		Bundle extras = getIntent().getExtras();
		if (extras != null && extras.containsKey("cm")) {
			cm = (CourseManagerConnector) extras.getSerializable("cm");
			cm.setContext(this);
		} else
			cm = new CourseManagerConnector(prefs.getString("server", ""), this);
		
	}

	public void reload() {
		new ReloadTask().execute();
		// updateList(reloadWork());
	}

	/**
	 * Non-UI Thread
	 * 
	 * @return
	 */
	protected JSONObject reloadWork() throws JSONException{
		return new JSONObject();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		MenuInflater inflater = getMenuInflater();
		inflater.inflate(R.menu.main_menu, menu);
		return true;
	}

	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		// Handle item selection
		switch (item.getItemId()) {
		case R.id.menu_reload:
			reload();
			return true;
		case R.id.menu_settings:
			Intent myIntent = new Intent(this, Settings.class);
			startActivityForResult(myIntent, 0);
			return true;
		default:
			return super.onOptionsItemSelected(item);
		}
	}
	

	class ReloadTask extends AsyncTask<Void, Void, JSONObject> {
		ProgressDialog dialog;

		protected void onPreExecute() {
			setProgressBarIndeterminateVisibility(true);
		}

		protected JSONObject doInBackground(Void... unused) {
			try {
				return reloadWork();
			} catch (JSONException e) {
				return new JSONObject();
			}
		}

		protected void onPostExecute(JSONObject list) {
			try {
				gotData(list);
				cm.toastFlashes();
			} catch (JSONException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			setProgressBarIndeterminateVisibility(false);

		}
	}

	/**
	 * UI Thread
	 * 
	 * @param data
	 */
	public void gotData(JSONObject data) throws JSONException {

	}

}
