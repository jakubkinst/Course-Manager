package cz.kinst.jakub.coursemanager;

import java.text.DateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.AlertDialog;
import android.app.Dialog;
import android.content.Context;
import android.content.DialogInterface;
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
import android.widget.ArrayAdapter;
import android.widget.EditText;
import android.widget.ListView;
import android.widget.TextView;
import cz.kinst.jakub.coursemanager.utils.DownloadTask;
import cz.kinst.jakub.coursemanager.utils.Utils;

/**
 * Activity showing detail page of a Lesson Uses tab interface: Tab 1: Lesson
 * Comments Tab 2: Lesson Resources
 * 
 * @author Jakub Kinst
 * 
 */
public class Lesson extends CMActivity {

	/**
	 * UID for serialization
	 */
	private static final long serialVersionUID = 5959554373918072957L;

	/**
	 * Lesson ID
	 */
	private int lid;

	public int MENU_NEW_COMMENT;
	private static final int DIALOG_NEW_COMMENT = 0;

	/**
	 * Comments tab name
	 */
	public static final String TAB_COMMENTS = "comments";

	/**
	 * Resources tab name
	 */
	public static final String TAB_RESOURCES = "resources";

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);

		// create tab structure
		setHeader(R.layout.lesson_header);
		addTab(TAB_COMMENTS, R.layout.lesson_comments,
				getText(R.string.comments));
		addTab(TAB_RESOURCES, R.layout.lesson_resources,
				getText(R.string.resources));

		// default tab
		switchTab(TAB_COMMENTS);

		// get Lesson ID from intent
		this.lid = getIntent().getExtras().getInt("lid");
		((TextView) (getTab(TAB_COMMENTS).findViewById(R.id.category)
				.findViewById(R.id.name))).setText(R.string.comments);
		((TextView) (getTab(TAB_RESOURCES).findViewById(R.id.category)
				.findViewById(R.id.name))).setText(R.string.resources);
		reload();
	}

	@Override
	protected Dialog onCreateDialog(int id) {
		Dialog dialog;
		switch (id) {
		case DIALOG_NEW_COMMENT:
			AlertDialog.Builder builder = new AlertDialog.Builder(this);
			final EditText input = new EditText(this);
			input.setInputType(InputType.TYPE_TEXT_FLAG_MULTI_LINE);
			input.setSingleLine(false);
			builder.setMessage(R.string.new_comment)
					.setView(input)
					.setCancelable(false)
					.setPositiveButton(R.string.post,
							new DialogInterface.OnClickListener() {
								@Override
								public void onClick(DialogInterface dialog,
										int id) {
									addComment(input.getText());
									input.setText("");
								}
							})
					.setNegativeButton(R.string.cancel,
							new DialogInterface.OnClickListener() {
								@Override
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

	/**
	 * Posts Web form to add comment to a current lesson
	 * 
	 * @param text
	 *            Comment content
	 */
	protected void addComment(Editable text) {
		final ArrayList<NameValuePair> postArgs = new ArrayList<NameValuePair>();
		postArgs.add(new BasicNameValuePair("content", text.toString()));

		final ArrayList<NameValuePair> getArgs = new ArrayList<NameValuePair>();
		getArgs.add(new BasicNameValuePair("lid", String.valueOf(lid)));

		// post comment in safe thread
		new AsyncTask<Void, Void, Void>() {
			@Override
			protected void onPreExecute() {
				setProgressBarIndeterminateVisibility(true);
			};

			@Override
			protected Void doInBackground(Void... params) {
				courseManagerCon.sendForm("lesson", "homepage", "commentForm",
						getArgs, postArgs);
				return null;
			}

			@Override
			protected void onPostExecute(Void result) {
				setProgressBarIndeterminateVisibility(false);
				courseManagerCon.toastFlashes();
				reload();
			};
		}.execute();
	}

	@Override
	protected JSONObject reloadWork() throws JSONException {
		JSONObject lesson = new JSONObject();
		ArrayList<NameValuePair> args = new ArrayList<NameValuePair>();
		args.add(new BasicNameValuePair("lid", String.valueOf(this.lid)));
		args.add(new BasicNameValuePair("pages-page", String.valueOf(this.page)));
		lesson = courseManagerCon.getAction("lesson", "homepage", args,
				new ArrayList<NameValuePair>());
		return lesson;
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		boolean result = super.onCreateOptionsMenu(menu);

		// add New Comment button
		MenuItem newComment = menu.add(R.string.new_comment);
		this.MENU_NEW_COMMENT = newComment.getItemId();
		newComment.setIcon(R.drawable.ic_action_edit);
		if (Integer.valueOf(android.os.Build.VERSION.SDK) >= 11) {
			newComment.setShowAsAction(MenuItem.SHOW_AS_ACTION_ALWAYS);
		}
		return result;
	}

	@Override
	public boolean onMenuItemSelected(int featureId, MenuItem item) {
		if (item.getItemId() == MENU_NEW_COMMENT) {
			showDialog(DIALOG_NEW_COMMENT);
			return true;
		} else {
			return super.onMenuItemSelected(featureId, item);
		}

	}

	@Override
	public void gotData(JSONObject data) throws JSONException {

		setPaginator(data, getTab(TAB_COMMENTS));
		JSONObject course = data.getJSONObject("activeCourse");
		JSONObject lesson = data.getJSONObject("lesson");
		setTitle(course.getString("name") + " > " + lesson.getString("topic"));

		((TextView) (getHeader().findViewById(R.id.topic))).setText(lesson
				.getString("topic"));
		Date dDate = Utils.getDateFromDBString(lesson.getString("date"));
		((TextView) (getHeader().findViewById(R.id.date))).setText(DateFormat
				.getDateInstance().format(dDate));

		ArrayList<JSONObject> resources = Utils.getJSONObjectArray(data
				.getJSONArray("resources"));
		ArrayList<JSONObject> comments = Utils.getJSONObjectArray(data
				.getJSONArray("comments"));

		((ListView) (getTab(TAB_RESOURCES).findViewById(R.id.resources)))
				.setAdapter(new ResourceAdapter(this,
						android.R.layout.simple_list_item_1, resources));
		((ListView) (getTab(TAB_COMMENTS).findViewById(R.id.comments)))
				.setAdapter(new CommentsAdapter(this,
						android.R.layout.simple_list_item_1, comments));
	}

	/**
	 * ArrayAdapter for Resources ListView
	 * 
	 * @author Jakub Kinst
	 * 
	 */
	public class ResourceAdapter extends ArrayAdapter<JSONObject> {

		public ResourceAdapter(Context context, int textViewResourceId,
				List<JSONObject> objects) {
			super(context, textViewResourceId, objects);
		}

		@Override
		public View getView(int position, View convertView, ViewGroup parent) {
			View v = convertView;
			if (v == null) {
				LayoutInflater vi = (LayoutInflater) getSystemService(Context.LAYOUT_INFLATER_SERVICE);
				v = vi.inflate(R.layout.resource_row, null);
			}
			final JSONObject resource = getItem(position);
			try {
				((TextView) (v.findViewById(R.id.filename))).setText(resource
						.getString("name"));
				int size = resource.getInt("size") / 1024;
				((TextView) (v.findViewById(R.id.size))).setText(size + " KB");

			} catch (JSONException e) {
				e.printStackTrace();
			}

			v.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					new DownloadTask(Lesson.this, courseManagerCon)
							.execute(resource);
				}
			});

			return v;
		}
	}

	/**
	 * ArrayAdapter for comments ListView
	 * 
	 * @author Jakub Kinst
	 * 
	 */
	public class CommentsAdapter extends ArrayAdapter<JSONObject> {

		public CommentsAdapter(Context context, int textViewResourceId,
				List<JSONObject> objects) {
			super(context, textViewResourceId, objects);
		}

		@Override
		public View getView(int position, View convertView, ViewGroup parent) {
			View v = convertView;
			if (v == null) {
				LayoutInflater vi = (LayoutInflater) getSystemService(Context.LAYOUT_INFLATER_SERVICE);
				v = vi.inflate(R.layout.comment_row, null);
			}
			JSONObject comment = getItem(position);
			try {

				((TextView) (v.findViewById(R.id.content))).setText(comment
						.getString("content"));
				((TextView) (v.findViewById(R.id.added))).setText(comment
						.getString("added"));
				((TextView) (v.findViewById(R.id.author))).setText(comment
						.getJSONObject("user").getString("firstname")
						+ " "
						+ comment.getJSONObject("user").getString("lastname"));
			} catch (JSONException e) {
				e.printStackTrace();
			}
			return v;
		}
	}
}
