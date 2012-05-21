package cz.kinst.jakub.coursemanager;

import java.io.Serializable;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.ActionBar;
import android.app.ProgressDialog;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.AsyncTask;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.widget.Button;
import android.widget.TextView;
import cz.kinst.jakub.coursemanager.utils.TabbedActivity;

/**
 * Base CourseManager Activity Extends TabbedActivity - provides tab
 * functionality if needed Provides simple AsyncTask which should do the main
 * Activity work. This work is refreshable by clicking on provided refresh
 * button.
 * 
 * Also, when it is needed, activity provides paging functionality with
 * navigation
 * 
 * @author Jakub Kinst
 * 
 */
public class CMActivity extends TabbedActivity implements Serializable {

	/**
	 * Android 3.0 (Honeycomb) SDK level
	 */
	private static final int ANDROID_3_0_SDK_LEVEL = 11;

	/**
	 * Serialization UID
	 */
	private static final long serialVersionUID = 1494642907274259339L;

	/**
	 * CourseManager Web Application API key (**** SECRET ****)
	 */
	private static final String API_KEY = "h7orro8492873y984ycojhjfkhsalfhu3y4riu23p31p2osad";

	/**
	 * Implementation of CourseManagerConnector is responsible for getting data
	 * from server
	 */
	CourseManagerConnector courseManagerCon;

	/**
	 * Boolean indicating reload in progress
	 */
	boolean isLoading = false;

	/**
	 * Number of pages in paging system
	 */
	public int pages;

	/**
	 * Currently active page
	 */
	public int page = 1;

	@Override
	public void onCreate(Bundle savedInstanceState) {

		/*
		 * We need default loading spinner in title bar to indicate reload in
		 * progress
		 */
		requestWindowFeature(Window.FEATURE_INDETERMINATE_PROGRESS);

		/*
		 * Android 3.0+ Action Bar is available since Android 3.0 If device OS
		 * is higher, use some Action Bar features
		 */
		if (Integer.valueOf(android.os.Build.VERSION.SDK) >= ANDROID_3_0_SDK_LEVEL) {
			ActionBar actionBar = getActionBar();
			// actionBar.setDisplayShowTitleEnabled(false);
			actionBar.setLogo(R.drawable.ic_launcher);
			// actionBar.setDisplayUseLogoEnabled(true);
			if (!this.getClass().equals(CourseList.class)) {
				actionBar.setDisplayHomeAsUpEnabled(true);
			}

		}

		super.onCreate(savedInstanceState);

		SharedPreferences prefs = PreferenceManager
				.getDefaultSharedPreferences(this);

		// set default server
		if (!prefs.contains("server")) {
			prefs.edit().putString("server", "").commit();
		}

		// get CourseManagerConnector instance from Intent if it is available
		// Instance must be passed in order to preserve Cookies etc.
		Bundle extras = getIntent().getExtras();
		if (extras != null && extras.containsKey("cm")) {
			courseManagerCon = (CourseManagerConnector) extras
					.getSerializable("cm");
			courseManagerCon.setContext(this);
		}
		// if not available, create new instance
		else {
			courseManagerCon = new CourseManagerConnector(prefs.getString(
					"server", ""), API_KEY, this);
		}

	}

	/**
	 * This method is called when activity is shown and when user hits reload
	 * button
	 */
	public void reload() {
		new ReloadTask().execute();
	}

	/**
	 * Main work of the activity (done in Non-UI Thread)
	 * 
	 * @return
	 */
	protected JSONObject reloadWork() throws JSONException {
		return new JSONObject();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		MenuInflater inflater = getMenuInflater();
		// get mainmenu skeleton from xml
		inflater.inflate(R.menu.main_menu, menu);
		return true;
	}

	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		// Handle item selection
		switch (item.getItemId()) {
		// click on logo in action bar - go back in android activity stack
		case android.R.id.home:
			if (!this.getClass().equals(CourseList.class)) {
				finish();
			}
			return true;
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

	/**
	 * Main Activity reload task. By default, it is called when activity is
	 * shown and when user clicks on reload button. Used because we don't want
	 * UI thread to be busy (frozen) when making HTTP requests
	 * 
	 * @author Jakub Kinst
	 * 
	 */
	class ReloadTask extends AsyncTask<Void, Void, JSONObject> {
		ProgressDialog dialog;

		@Override
		protected void onPreExecute() {
			setProgressBarIndeterminateVisibility(true);
			courseManagerCon.getFlashMessages().clear();
		}

		@Override
		protected JSONObject doInBackground(Void... unused) {
			try {
				return reloadWork();
			} catch (JSONException e) {
				return new JSONObject();
			}
		}

		@Override
		protected void onPostExecute(JSONObject list) {
			try {
				courseManagerCon.toastFlashes();
				gotData(list);
			} catch (JSONException e) {
				e.printStackTrace();
			}
			setProgressBarIndeterminateVisibility(false);

		}
	}

	/**
	 * In this method the resulting data from Http request should be consumed.
	 * (done in UI Thread)
	 * 
	 * @param data
	 */
	public void gotData(JSONObject data) throws JSONException {

	}

	/**
	 * Initializes Paging system
	 * 
	 * @param data
	 *            Data from JSON response
	 * @throws JSONException
	 */
	protected void initPaginator(JSONObject data) throws JSONException {
		setPaginator(data, null);
	}

	/**
	 * Initializes Paging system
	 * 
	 * @param data
	 *            Data from JSON response
	 * @param view
	 *            paging panel view
	 * @throws JSONException
	 */
	protected void setPaginator(JSONObject data, View view)
			throws JSONException {
		boolean act = view == null;
		Button prev = act ? (Button) this.findViewById(R.id.previous_page)
				: (Button) view.findViewById(R.id.previous_page);
		Button next = act ? (Button) this.findViewById(R.id.nextt_page)
				: (Button) view.findViewById(R.id.nextt_page);
		TextView pageLabel = act ? (TextView) this.findViewById(R.id.page)
				: (TextView) view.findViewById(R.id.page);
		if (data.getJSONObject("pages").has("steps")) {
			JSONArray steps = data.getJSONObject("pages").getJSONArray("steps");
			pages = steps.length();

			pageLabel.setText(getText(R.string.page) + " "
					+ String.valueOf(page) + " " + getText(R.string.of) + " "
					+ pages);
			if (page < 2) {
				prev.setVisibility(View.INVISIBLE);
			} else {
				prev.setVisibility(View.VISIBLE);
			}
			if (page >= pages) {
				next.setVisibility(View.INVISIBLE);
			} else {
				next.setVisibility(View.VISIBLE);
			}

			pageLabel.setVisibility(View.VISIBLE);

			prev.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					page = page - 1;
					reload();
				}
			});
			next.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					page = page + 1;
					reload();
				}
			});
		} else {
			prev.setVisibility(View.GONE);
			next.setVisibility(View.GONE);
			pageLabel.setVisibility(View.GONE);
		}

	}

}
