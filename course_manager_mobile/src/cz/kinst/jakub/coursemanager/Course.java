package cz.kinst.jakub.coursemanager;

import java.io.Serializable;
import java.util.ArrayList;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.webkit.WebView;
import android.widget.BaseExpandableListAdapter;
import android.widget.Button;
import android.widget.ExpandableListView;
import android.widget.TextView;

public class Course extends CMActivity implements Serializable {

	private static final String TAB_LESSONS = "lessons";
	private static final String TAB_FORUM = "forum";
	private static final String TAB_EVENTS = "events";
	private int cid;
	private final int MENU_FORUM = 0;
	private final int MENU_EVENTS = 1;

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		this.cid = getIntent().getExtras().getInt("cid");
		addTab(TAB_LESSONS, R.layout.course, getText(R.string.lesson));
		switchTab(TAB_LESSONS);
		reload();
	}

	@Override
	protected JSONObject reloadWork() {
		JSONObject course = new JSONObject();
		ArrayList<NameValuePair> args = new ArrayList<NameValuePair>();
		args.add(new BasicNameValuePair("cid", String.valueOf(this.cid)));
		course = courseManagerCon.getAction("course", "homepage", args,
				new ArrayList<NameValuePair>());
		return course;
	}

	@Override
	public void gotData(JSONObject data) throws JSONException {
		JSONObject course = data.getJSONObject("activeCourse");
		String name = course.getString("name");
		String description = course.getString("description");
		((TextView) findViewById(R.id.name)).setText(name);
		((TextView) findViewById(R.id.description)).setText(description);

		ArrayList<JSONObject> list = new ArrayList<JSONObject>();
		JSONArray lessons = data.getJSONArray("lessons");
		for (int i = 0; i < lessons.length(); i++) {
			list.add(lessons.getJSONObject(i));
		}
		ExpandableListView lessonView = (ExpandableListView) findViewById(R.id.lessons);
		lessonView.setAdapter(new LessonListAdapter(list));
		if (list.size() > 0)
			lessonView.expandGroup(0);
	}

	public class LessonListAdapter extends BaseExpandableListAdapter {

		public ArrayList<JSONObject> lessons;

		public LessonListAdapter(ArrayList<JSONObject> lessons) {
			this.lessons = lessons;
		}

		@Override
		public Object getChild(int groupPosition, int childPosition) {
			return lessons.get(groupPosition);
		}

		@Override
		public long getChildId(int groupPosition, int childPosition) {
			return childPosition;
		}

		@Override
		public View getChildView(int groupPosition, int childPosition,
				boolean isLastChild, View convertView, ViewGroup parent) {
			View v = convertView;
			if (v == null) {
				LayoutInflater vi = (LayoutInflater) getSystemService(Context.LAYOUT_INFLATER_SERVICE);
				v = vi.inflate(R.layout.lesson_content_row, null);
			}

			WebView content = (WebView) v.findViewById(R.id.content);
			Button detail = (Button) v.findViewById(R.id.show_more_button);

			content.setVerticalScrollBarEnabled(true);
			content.setHorizontalScrollBarEnabled(true);
			final JSONObject lesson;
			lesson = lessons.get(groupPosition);
			String html = "";
			try {
				html = "<html><body>" + lesson.getString("description_html")
						+ "</body></html>";
			} catch (JSONException e) {
				e.printStackTrace();
			}
			detail.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					Intent i = new Intent(Course.this, Lesson.class);
					try {
						i.putExtra("lid", lesson.getInt("id"));
					} catch (JSONException e) {
						// TODO Auto-generated catch block
						e.printStackTrace();
					}
					i.putExtra("cm", courseManagerCon);
					startActivity(i);

				}
			});

			content.loadData(html, "text/html", null);
			return v;
		}

		@Override
		public int getChildrenCount(int groupPosition) {
			return 1;
		}

		@Override
		public Object getGroup(int groupPosition) {
			return lessons.get(groupPosition);
		}

		@Override
		public int getGroupCount() {
			return lessons.size();
		}

		@Override
		public long getGroupId(int groupPosition) {
			return groupPosition;
		}

		@Override
		public boolean isEmpty() {
			return lessons.size() == 0;
		}

		@Override
		public View getGroupView(int groupPosition, boolean isExpanded,
				View convertView, ViewGroup parent) {
			View v = convertView;
			if (v == null) {
				LayoutInflater vi = (LayoutInflater) getSystemService(Context.LAYOUT_INFLATER_SERVICE);
				v = vi.inflate(R.layout.lesson_header_row, null);
			}

			TextView topic = (TextView) v.findViewById(R.id.topic);
			TextView date = (TextView) v.findViewById(R.id.date);

			final JSONObject lesson;
			lesson = lessons.get(groupPosition);
			try {
				topic.setText(lesson.getString("topic"));
				date.setText(lesson.getString("date"));
			} catch (JSONException e) {
			}
			return v;
		}

		@Override
		public boolean hasStableIds() {
			// TODO Auto-generated method stub
			return false;
		}

		@Override
		public boolean isChildSelectable(int groupPosition, int childPosition) {
			// TODO Auto-generated method stub
			return true;
		}

	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		boolean result = super.onCreateOptionsMenu(menu);

		MenuItem forum = menu.add(0, MENU_FORUM, 0, R.string.forum);
		forum.setIcon(R.drawable.ic_action_forum);
		if (Integer.valueOf(android.os.Build.VERSION.SDK) >= 11)
			forum.setShowAsAction(MenuItem.SHOW_AS_ACTION_ALWAYS);

		MenuItem events = menu.add(0, MENU_EVENTS, 0, R.string.events);
		events.setIcon(R.drawable.ic_action_events);
		if (Integer.valueOf(android.os.Build.VERSION.SDK) >= 11)
			events.setShowAsAction(MenuItem.SHOW_AS_ACTION_ALWAYS);

		return result;
	}

	@Override
	public boolean onMenuItemSelected(int featureId, MenuItem item) {
		if (item.getItemId() == MENU_FORUM) {
			startActivity(new Intent(this, Forum.class).putExtra("cm",
					courseManagerCon).putExtra("cid", cid));
			return true;
		} else if (item.getItemId() == MENU_EVENTS) {
			startActivity(new Intent(this, Events.class).putExtra("cm",
					courseManagerCon).putExtra("cid", cid));
			return true;
		} else {
			return super.onMenuItemSelected(featureId, item);
		}

	}
}
