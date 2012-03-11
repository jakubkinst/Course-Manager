package cz.kinst.jakub.coursemanager;

import java.util.ArrayList;
import java.util.List;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.AlertDialog;
import android.app.Dialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.text.Editable;
import android.text.InputType;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.view.ViewGroup.LayoutParams;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;
import cz.kinst.jakub.coursemanager.utils.DownloadTask;

public class Forum extends CMActivity {

	private static final int DIALOG_NEW_TOPIC = 0;
	private int cid;
	public int MENU_NEW_TOPIC;

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		this.cid = getIntent().getExtras().getInt("cid");
		setContentView(R.layout.forum_topics);
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
				courseManagerCon.sendForm("forum", "homepage", "addTopic",
						getArgs, postArgs);
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
		forum = courseManagerCon.getAction("forum", "homepage", args,
				new ArrayList<NameValuePair>());
		return forum;
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		boolean result = super.onCreateOptionsMenu(menu);
		MenuItem newComment = menu.add(R.string.new_topic);
		this.MENU_NEW_TOPIC = newComment.getItemId();
		newComment.setIcon(android.R.drawable.ic_menu_edit);
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
		JSONArray resourcesJSON = data.getJSONArray("topics");
		for (int i = 0; i < resourcesJSON.length(); i++) {
			topics.add(resourcesJSON.getJSONObject(i));
		}
		((ListView) (findViewById(R.id.topics))).setAdapter(new TopicsAdapter(
				this, android.R.layout.simple_list_item_1, topics));
	}

	public class TopicsAdapter extends ArrayAdapter<JSONObject> {

		public List<JSONObject> threads;

		public TopicsAdapter(Context context, int textViewResourceId,
				List<JSONObject> objects) {
			super(context, textViewResourceId, objects);
			this.threads = objects;
		}

		@Override
		public View getView(int position, View convertView, ViewGroup parent) {
			View v = convertView;
			if (v == null) {
				LayoutInflater vi = (LayoutInflater) getSystemService(Context.LAYOUT_INFLATER_SERVICE);
				v = vi.inflate(R.layout.forum_topic_row, null);
			}
			final JSONObject topic = threads.get(position);
			try {
				String lastReply = topic.getString("lastreply");
				if (lastReply.equals("false"))
					lastReply = topic.getString("created");
				else
					lastReply = topic.getJSONObject("lastreply").getString(
							"created");

				((TextView) (v.findViewById(R.id.label))).setText(topic
						.getString("label"));
				((TextView) (v.findViewById(R.id.lastReply)))
						.setText(lastReply);
				((TextView) (v.findViewById(R.id.count))).setText("("
						+ topic.getString("replies") + ")");

			} catch (JSONException e) {
				e.printStackTrace();
			}

			v.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					Intent i = new Intent(Forum.this, ForumReplies.class);
					try {
						i.putExtra("tid", topic.getInt("id"));
					} catch (JSONException e) {
						// TODO Auto-generated catch block
						e.printStackTrace();
					}
					i.putExtra("cm", courseManagerCon);
					startActivity(i);
				}
			});

			return v;
		}
	}

}
