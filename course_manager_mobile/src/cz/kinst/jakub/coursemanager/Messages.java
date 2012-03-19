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

import android.content.Context;
import android.content.Intent;
import android.graphics.Typeface;
import android.os.AsyncTask;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ListView;
import android.widget.TextView;
import cz.kinst.jakub.coursemanager.utils.Utils;

public class Messages extends CMActivity {

	/**
	 * UID for serialization
	 */
	private static final long serialVersionUID = -5260322349563369526L;
	private static final int MENU_NEW_MESSAGE = 0;
	private static final String TAB_INBOX = "inbox";
	private static final String TAB_OUTBOX = "outbox";

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		addTab(TAB_INBOX, R.layout.messages, getText(R.string.inbox));
		addTab(TAB_OUTBOX, R.layout.messages, getText(R.string.outbox));
		switchTab(TAB_INBOX);
	}

	@Override
	public void reload() {
		switchTab(getActiveTab());
	}

	@Override
	protected void onTabSwitched(Tab t) {
		super.onTabSwitched(t);
		if (t.getName().equals(TAB_INBOX)) {
			reloadInbox();
		} else if (t.getName().equals(TAB_OUTBOX)) {
			reloadOutbox();
		}
	}

	protected void reloadInbox() {
		new AsyncTask<Void, Void, JSONObject>() {
			@Override
			protected void onPreExecute() {
				setProgressBarIndeterminateVisibility(true);
			};

			@Override
			protected JSONObject doInBackground(Void... params) {
				JSONObject forum = new JSONObject();
				ArrayList<NameValuePair> args = new ArrayList<NameValuePair>();
				args.add(new BasicNameValuePair("pages-page", String
						.valueOf(page)));
				forum = courseManagerCon.getAction("message", "inbox", args,
						new ArrayList<NameValuePair>());
				return forum;
			}

			@Override
			protected void onPostExecute(JSONObject result) {
				ArrayList<JSONObject> messages = new ArrayList<JSONObject>();
				JSONArray messagesJSON;
				try {
					messagesJSON = result.getJSONArray("inbox");

					for (int i = 0; i < messagesJSON.length(); i++) {
						messages.add(messagesJSON.getJSONObject(i));
					}
					((ListView) (findViewById(R.id.messages)))
							.setAdapter(new InboxAdapter(Messages.this,
									android.R.layout.simple_list_item_1,
									messages));
				} catch (JSONException e) {
					e.printStackTrace();
				}
				setProgressBarIndeterminateVisibility(false);
			};
		}.execute();
	}

	protected void reloadOutbox() {
		new AsyncTask<Void, Void, JSONObject>() {
			@Override
			protected void onPreExecute() {
				setProgressBarIndeterminateVisibility(true);
			};

			@Override
			protected JSONObject doInBackground(Void... params) {
				JSONObject forum = new JSONObject();
				ArrayList<NameValuePair> args = new ArrayList<NameValuePair>();
				args.add(new BasicNameValuePair("pages-page", String
						.valueOf(page)));
				forum = courseManagerCon.getAction("message", "outbox", args,
						new ArrayList<NameValuePair>());
				return forum;
			}

			@Override
			protected void onPostExecute(JSONObject result) {
				ArrayList<JSONObject> messages = new ArrayList<JSONObject>();
				JSONArray messagesJSON;
				try {
					messagesJSON = result.getJSONArray("outbox");

					for (int i = 0; i < messagesJSON.length(); i++) {
						messages.add(messagesJSON.getJSONObject(i));
					}
					((ListView) (findViewById(R.id.messages)))
							.setAdapter(new OutboxAdapter(Messages.this,
									android.R.layout.simple_list_item_1,
									messages));
				} catch (JSONException e) {
					e.printStackTrace();
				}
				setProgressBarIndeterminateVisibility(false);
			};
		}.execute();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		boolean result = super.onCreateOptionsMenu(menu);
		MenuItem newComment = menu.add(0, MENU_NEW_MESSAGE, 0,
				R.string.new_message);
		newComment.setIcon(R.drawable.ic_action_edit);
		if (Integer.valueOf(android.os.Build.VERSION.SDK) >= 11) {
			newComment.setShowAsAction(MenuItem.SHOW_AS_ACTION_ALWAYS);
		}
		return result;
	}

	@Override
	public boolean onMenuItemSelected(int featureId, MenuItem item) {
		if (item.getItemId() == MENU_NEW_MESSAGE) {
			startActivity(new Intent(this, MessagesNew.class).putExtra("cm",
					courseManagerCon));
			return true;
		} else {
			return super.onMenuItemSelected(featureId, item);
		}
	}

	@Override
	protected void onResume() {
		reload();
		super.onResume();
	}

	public class InboxAdapter extends ArrayAdapter<JSONObject> {

		public InboxAdapter(Context context, int textViewResourceId,
				List<JSONObject> objects) {
			super(context, textViewResourceId, objects);
		}

		@Override
		public View getView(int position, View convertView, ViewGroup parent) {
			View v = convertView;
			if (v == null) {
				LayoutInflater vi = (LayoutInflater) getSystemService(Context.LAYOUT_INFLATER_SERVICE);
				v = vi.inflate(R.layout.messages_message_row, null);
			}
			final JSONObject message = getItem(position);
			try {
				boolean read = message.getString("read").equals("1");

				((TextView) (v.findViewById(R.id.subject))).setText(message
						.getString("subject"));
				if (!read) {
					((TextView) (v.findViewById(R.id.subject))).setTypeface(
							null, Typeface.BOLD);
				} else {
					((TextView) (v.findViewById(R.id.subject))).setTypeface(
							null, Typeface.NORMAL);
				}
				((TextView) (v.findViewById(R.id.from))).setText(message
						.getJSONObject("from").getString("firstname")
						+ " "
						+ message.getJSONObject("from").getString("lastname"));
				Date date = Utils
						.getDateFromDBString(message.getString("sent"));
				((TextView) (v.findViewById(R.id.date))).setText(DateFormat
						.getDateInstance().format(date.getTime()));

			} catch (JSONException e) {
				e.printStackTrace();
			}
			v.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					Intent i = new Intent(Messages.this, MessagesShow.class);
					i.putExtra("message", message.toString());
					startActivity(i);
				}
			});
			return v;
		}
	}

	public class OutboxAdapter extends ArrayAdapter<JSONObject> {

		public List<JSONObject> messages;

		public OutboxAdapter(Context context, int textViewResourceId,
				List<JSONObject> objects) {
			super(context, textViewResourceId, objects);
			this.messages = objects;
		}

		@Override
		public View getView(int position, View convertView, ViewGroup parent) {
			View v = convertView;
			if (v == null) {
				LayoutInflater vi = (LayoutInflater) getSystemService(Context.LAYOUT_INFLATER_SERVICE);
				v = vi.inflate(R.layout.messages_message_row, null);
			}
			final JSONObject message = messages.get(position);
			try {
				((TextView) (v.findViewById(R.id.subject))).setText(message
						.getString("subject"));
				((TextView) (v.findViewById(R.id.from))).setText(message
						.getJSONObject("to").getString("firstname")
						+ " "
						+ message.getJSONObject("to").getString("lastname"));
				Date date = Utils
						.getDateFromDBString(message.getString("sent"));
				((TextView) (v.findViewById(R.id.date))).setText(DateFormat
						.getDateInstance().format(date.getTime()));

			} catch (JSONException e) {
				e.printStackTrace();
			}
			v.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					Intent i = new Intent(Messages.this, MessagesShow.class);
					i.putExtra("message", message.toString());
					startActivity(i);
				}
			});
			return v;
		}
	}

}
