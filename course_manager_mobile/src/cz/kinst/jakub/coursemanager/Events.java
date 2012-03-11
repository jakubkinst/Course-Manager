package cz.kinst.jakub.coursemanager;

import java.text.DateFormat;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.List;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import cz.kinst.jakub.coursemanager.utils.Utils;

import android.app.AlertDialog;
import android.app.Dialog;
import android.content.ContentResolver;
import android.content.ContentValues;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.database.Cursor;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Bundle;
import android.provider.CalendarContract;
import android.provider.CalendarContract.Calendars;
import android.view.ContextMenu;
import android.view.ContextMenu.ContextMenuInfo;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.EditText;
import android.widget.ListView;
import android.widget.TextView;

public class Events extends CMActivity {

	private static final int DIALOG_NEW_TOPIC = 0;
	private static final String CALENDAR_NAME = "Course Manager";
	private int cid;
	public int MENU_NEW_TOPIC;

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		this.cid = getIntent().getExtras().getInt("cid");
		setContentView(R.layout.events);
		reload();
	}

	protected Dialog onCreateDialog(int id) {
		Dialog dialog;
		switch (id) {
		case DIALOG_NEW_TOPIC:
			LayoutInflater factory = LayoutInflater.from(this);
			final View v = factory.inflate(R.layout.dialog_new_topic, null);
			final EditText inputLabel = (EditText) v.findViewById(R.id.label);
			final EditText inputContent = (EditText) v
					.findViewById(R.id.content);
			AlertDialog.Builder builder = new AlertDialog.Builder(this);
			builder.setMessage(R.string.new_topic)
					.setView(v)
					.setCancelable(false)
					.setPositiveButton(R.string.post,
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int id) {
									addTopic(inputLabel.getText().toString(),
											inputContent.getText().toString());
									inputLabel.setText("");
									inputContent.setText("");
								}
							})
					.setNegativeButton(R.string.cancel,
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int id) {
									dialog.cancel();
								}
							});
			dialog = builder.create();
			break;
		default:
			dialog = null;
		}
		return dialog;
	}

	protected void addTopic(String label, String content) {
		final ArrayList<NameValuePair> postArgs = new ArrayList<NameValuePair>();
		postArgs.add(new BasicNameValuePair("label", label));
		postArgs.add(new BasicNameValuePair("content", content));
		final ArrayList<NameValuePair> getArgs = new ArrayList<NameValuePair>();
		getArgs.add(new BasicNameValuePair("cid", String.valueOf(cid)));

		// post topic in safe thread
		new AsyncTask<Void, Void, Void>() {
			protected void onPreExecute() {
				setProgressBarIndeterminateVisibility(true);
			};

			protected Void doInBackground(Void... params) {
				courseManagerCon.sendForm("forum", "homepage", "addTopic", getArgs, postArgs);
				return null;
			}

			protected void onPostExecute(Void result) {
				setProgressBarIndeterminateVisibility(false);
				courseManagerCon.toastFlashes();
				reload();
			};
		}.execute();
	}

	@Override
	protected JSONObject reloadWork() throws JSONException {
		JSONObject forum = new JSONObject();
		ArrayList<NameValuePair> args = new ArrayList<NameValuePair>();
		args.add(new BasicNameValuePair("cid", String.valueOf(this.cid)));
		args.add(new BasicNameValuePair("pages-page", String.valueOf(this.page)));
		forum = courseManagerCon.getAction("event", "homepage", args,
				new ArrayList<NameValuePair>());
		return forum;
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		boolean result = super.onCreateOptionsMenu(menu);
		MenuItem newComment = menu.add(R.string.new_topic);
		this.MENU_NEW_TOPIC = newComment.getItemId();
		newComment.setIcon(R.drawable.ic_action_add);
		if (Integer.valueOf(android.os.Build.VERSION.SDK) >= 11)
			newComment.setShowAsAction(MenuItem.SHOW_AS_ACTION_ALWAYS);
		return result;
	}

	@Override
	public boolean onMenuItemSelected(int featureId, MenuItem item) {
		if (item.getItemId() == MENU_NEW_TOPIC) {
			showDialog(DIALOG_NEW_TOPIC);
			return true;
		} else {
			return super.onMenuItemSelected(featureId, item);
		}
	}

	@Override
	public void gotData(JSONObject data) throws JSONException {
		setPaginator(data);
		ArrayList<JSONObject> topics = new ArrayList<JSONObject>();
		JSONArray resourcesJSON = data.getJSONArray("events");
		for (int i = 0; i < resourcesJSON.length(); i++) {
			topics.add(resourcesJSON.getJSONObject(i));
		}
		((ListView) (findViewById(R.id.events))).setAdapter(new EventsAdapter(
				this, android.R.layout.simple_list_item_1, topics));

		registerForContextMenu(((ListView) (findViewById(R.id.events))));
	}

	public class EventsAdapter extends ArrayAdapter<JSONObject> {

		public List<JSONObject> events;

		public EventsAdapter(Context context, int textViewResourceId,
				List<JSONObject> objects) {
			super(context, textViewResourceId, objects);
			this.events = objects;
		}

		@Override
		public View getView(int position, View convertView, ViewGroup parent) {
			View v = convertView;
			if (v == null) {
				LayoutInflater vi = (LayoutInflater) getSystemService(Context.LAYOUT_INFLATER_SERVICE);
				v = vi.inflate(R.layout.events_event_row, null);
			}
			final JSONObject event = events.get(position);
			try {
				((TextView) (v.findViewById(R.id.name))).setText(event
						.getString("name"));
				((TextView) (v.findViewById(R.id.description))).setText(event
						.getString("description"));
				Date date = Utils.getDateFromDBString(event.getString("date"));
				((TextView) (v.findViewById(R.id.date))).setText(DateFormat
						.getDateInstance().format(date.getTime()));

				v.setTag(event);

			} catch (JSONException e) {
				e.printStackTrace();
			}
			return v;
		}
	}

	@Override
	public void onCreateContextMenu(ContextMenu menu, View v,
			ContextMenuInfo menuInfo) {
		super.onCreateContextMenu(menu, v, menuInfo);
		getMenuInflater().inflate(R.menu.events_context_menu, menu);
	}

	@Override
	public boolean onContextItemSelected(MenuItem item) {
		AdapterView.AdapterContextMenuInfo info = (AdapterView.AdapterContextMenuInfo) item
				.getMenuInfo();
		JSONObject event = (JSONObject) info.targetView.getTag();
		switch (item.getItemId()) {
		case R.id.toCalendar:
			try {
				Calendar cal = Calendar.getInstance();
				Intent intent = new Intent(Intent.ACTION_EDIT);
				intent.setType("vnd.android.cursor.item/event");
				intent.putExtra("beginTime",Utils.getDateFromDBString(event.getString("date")).getTime());
				intent.putExtra("allDay", true);
				//intent.putExtra("rrule", "FREQ=YEARLY");
				intent.putExtra("endTime",Utils.getDateFromDBString(event.getString("date")).getTime());
				intent.putExtra("title", event.getString("name"));
				intent.putExtra("description", event.getString("description"));

				startActivity(intent);
			} catch (JSONException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			return true;
		default:
			return super.onContextItemSelected(item);
		}
	}

	/**
	 * NOT USED
	 * 
	 * @return
	 */
	private long createCMCalendar() {
		ContentResolver cr = getContentResolver();
		ContentValues values = new ContentValues();
		values.put(Calendars.ACCOUNT_TYPE, CalendarContract.ACCOUNT_TYPE_LOCAL);
		values.put(Calendars.NAME, Events.CALENDAR_NAME);
		Uri uri = cr.insert(Calendars.CONTENT_URI, values);

		// get the event ID that is the last element in the Uri
		return Long.parseLong(uri.getLastPathSegment());
	}

	/**
	 * NOT USED
	 * 
	 * @return
	 */
	private long getCMCalendarId() {
		final String[] EVENT_PROJECTION = new String[] { Calendars._ID, // 0
				Calendars.ACCOUNT_NAME, // 1
				Calendars.CALENDAR_DISPLAY_NAME // 2
		};

		// The indices for the projection array above.
		final int PROJECTION_ID_INDEX = 0;
		final int PROJECTION_ACCOUNT_NAME_INDEX = 1;
		final int PROJECTION_DISPLAY_NAME_INDEX = 2;
		// Run query
		Cursor cur = null;
		ContentResolver cr = getContentResolver();
		Uri uri = Calendars.CONTENT_URI;
		// Submit the query and get a Cursor object back.
		cur = cr.query(uri, EVENT_PROJECTION, null, null, null);
		while (cur.moveToNext()) {
			long calID = 0;
			String displayName = null;
			calID = cur.getLong(PROJECTION_ID_INDEX);
			displayName = cur.getString(PROJECTION_DISPLAY_NAME_INDEX);
			if (displayName.equals(Events.CALENDAR_NAME))
				return calID;
		}
		return -1;
	}

}
