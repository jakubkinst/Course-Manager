package cz.kinst.jakub.coursemanager;

import java.text.DateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.content.ContentResolver;
import android.content.ContentValues;
import android.content.Context;
import android.content.Intent;
import android.database.Cursor;
import android.net.Uri;
import android.os.Bundle;
import android.provider.CalendarContract;
import android.provider.CalendarContract.Calendars;
import android.view.ContextMenu;
import android.view.ContextMenu.ContextMenuInfo;
import android.view.LayoutInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.ListView;
import android.widget.TextView;
import cz.kinst.jakub.coursemanager.utils.Utils;

/**
 * 
 * Activity showing list of course events. User is able to copy event to device
 * calendar by long-tapping the event list item
 * 
 * @author Jakub Kinst
 * 
 */
public class Events extends CMActivity {

	/**
	 * UID for serialization
	 */
	private static final long serialVersionUID = 5108528581652454927L;

	/**
	 * NOT USED Custom Calendar Name
	 */
	private static final String CALENDAR_NAME = "Course Manager";

	/**
	 * Course ID
	 */
	private int cid;

	// MENU constants
	public int MENU_NEW_TOPIC;

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		this.cid = getIntent().getExtras().getInt("cid");
		setContentView(R.layout.events);
		reload();
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
	public void gotData(JSONObject data) throws JSONException {
		initPaginator(data);
		JSONObject course = data.getJSONObject("activeCourse");
		setTitle(course.getString("name") + " > " + getText(R.string.events));
		ArrayList<JSONObject> topics = new ArrayList<JSONObject>();
		JSONArray resourcesJSON = data.getJSONArray("events");
		for (int i = 0; i < resourcesJSON.length(); i++) {
			topics.add(resourcesJSON.getJSONObject(i));
		}
		((ListView) (findViewById(R.id.events))).setAdapter(new EventsAdapter(
				this, android.R.layout.simple_list_item_1, topics));

		registerForContextMenu((findViewById(R.id.events)));
	}

	/**
	 * ArrayAdapter for Events ListView
	 * 
	 * @author Jakub Kinst
	 * 
	 */
	public class EventsAdapter extends ArrayAdapter<JSONObject> {

		public EventsAdapter(Context context, int textViewResourceId,
				List<JSONObject> objects) {
			super(context, textViewResourceId, objects);
		}

		@Override
		public View getView(int position, View convertView, ViewGroup parent) {
			View v = convertView;
			if (v == null) {
				LayoutInflater vi = (LayoutInflater) getSystemService(Context.LAYOUT_INFLATER_SERVICE);
				v = vi.inflate(R.layout.events_event_row, null);
			}
			final JSONObject event = getItem(position);
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
	/**
	 * Context menu on long-press on Event
	 */
	public void onCreateContextMenu(ContextMenu menu, View v,
			ContextMenuInfo menuInfo) {
		super.onCreateContextMenu(menu, v, menuInfo);
		getMenuInflater().inflate(R.menu.events_context_menu, menu);
	}

	@Override
	public boolean onContextItemSelected(MenuItem item) {

		// SAVE Event to Device calendar via Intent

		AdapterView.AdapterContextMenuInfo info = (AdapterView.AdapterContextMenuInfo) item
				.getMenuInfo();
		JSONObject event = (JSONObject) info.targetView.getTag();
		switch (item.getItemId()) {
		case R.id.toCalendar:
			try {
				Intent intent = new Intent(Intent.ACTION_EDIT);
				intent.setType("vnd.android.cursor.item/event");
				intent.putExtra("beginTime",
						Utils.getDateFromDBString(event.getString("date"))
								.getTime());
				intent.putExtra("allDay", true);
				intent.putExtra("endTime",
						Utils.getDateFromDBString(event.getString("date"))
								.getTime());
				intent.putExtra("title", event.getString("name"));
				intent.putExtra("description", event.getString("description"));

				startActivity(intent);
			} catch (JSONException e) {
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
	@SuppressWarnings("unused")
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
	@SuppressWarnings("unused")
	private long getCMCalendarId() {
		final String[] EVENT_PROJECTION = new String[] { Calendars._ID, // 0
				Calendars.ACCOUNT_NAME, // 1
				Calendars.CALENDAR_DISPLAY_NAME // 2
		};

		// The indices for the projection array above.
		final int PROJECTION_ID_INDEX = 0;
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
			if (displayName.equals(Events.CALENDAR_NAME)) {
				return calID;
			}
		}
		return -1;
	}

}
