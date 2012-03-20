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

public class CMActivity extends TabbedActivity implements Serializable {

	private static final long serialVersionUID = 1494642907274259339L;
	CourseManagerConnector courseManagerCon;
	boolean isLoading = false;
	public int pages;
	public int page = 1;

	@Override
	public void onCreate(Bundle savedInstanceState) {

		requestWindowFeature(Window.FEATURE_INDETERMINATE_PROGRESS);

		// Android 3.0+
		if (Integer.valueOf(android.os.Build.VERSION.SDK) >= 11) {
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
		if (!prefs.contains("server")) {
			prefs.edit().putString("server", "").commit();
		}
		Bundle extras = getIntent().getExtras();
		if (extras != null && extras.containsKey("cm")) {
			courseManagerCon = (CourseManagerConnector) extras
					.getSerializable("cm");
			courseManagerCon.setContext(this);
		} else {
			courseManagerCon = new CourseManagerConnector(prefs.getString(
					"server", ""), this);
		}

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
	protected JSONObject reloadWork() throws JSONException {
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
	 * UI Thread
	 * 
	 * @param data
	 */
	public void gotData(JSONObject data) throws JSONException {

	}

	protected void setPaginator(JSONObject data) throws JSONException {
		setPaginator(data, null);
	}

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
