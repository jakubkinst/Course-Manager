package cz.kinst.jakub.coursemanager;

import java.io.Serializable;
import java.util.ArrayList;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.view.ContextMenu;
import android.view.ContextMenu.ContextMenuInfo;
import android.view.LayoutInflater;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView.AdapterContextMenuInfo;
import android.widget.BaseExpandableListAdapter;
import android.widget.ExpandableListView;
import android.widget.Toast;
import android.widget.ExpandableListView.OnChildClickListener;
import android.widget.TextView;

public class CourseList extends CMActivity implements Serializable {

	/**
	 * 
	 */
	private static final long serialVersionUID = 6232480315437451355L;

	@Override
	public void onCreate(Bundle savedInstanceState) {

		super.onCreate(savedInstanceState);
		setContentView(R.layout.courselist);
		reload();
	}

	@Override
	protected JSONObject reloadWork() {
		JSONObject resp = new JSONObject();
		resp = cm.getAction("courselist");
		return resp;
	}

	// UI Thread
	@Override
	public void gotData(JSONObject data) throws JSONException {
		ArrayList<JSONObject> tCourses = new ArrayList<JSONObject>();
		ArrayList<JSONObject> sCourses = new ArrayList<JSONObject>();

		JSONArray courses = data.getJSONArray("tCourses");
		for (int i = 0; i < courses.length(); i++) {
			tCourses.add(courses.getJSONObject(i));
		}
		courses = data.getJSONArray("sCourses");
		for (int i = 0; i < courses.length(); i++) {
			sCourses.add(courses.getJSONObject(i));
		}

		final CourseListAdapter adapter = new CourseListAdapter(tCourses,sCourses);
		ExpandableListView listView = (ExpandableListView) findViewById(R.id.list);
		registerForContextMenu(listView);
		listView.setAdapter(adapter);
		listView.expandGroup(0);
		listView.expandGroup(1);
		listView.setOnChildClickListener(new OnChildClickListener() {			
			@Override
			public boolean onChildClick(ExpandableListView parent, View v,
					int groupPosition, int childPosition, long id) {				
				try {
					Intent i = new Intent(CourseList.this, Course.class);					
					int cid;
					if (groupPosition==0)
						cid = adapter.tCourses.get(childPosition).getInt("id");
					else
						cid = adapter.sCourses.get(childPosition).getInt("id");
					i.putExtra("cid", cid);
					i.putExtra("cm", cm);
					startActivity(i);
				} catch (JSONException e) {
				}
				return true;
			}
		});
	}
	
	@Override
	public void onCreateContextMenu(ContextMenu menu, View v,
	                                ContextMenuInfo menuInfo) {
	    super.onCreateContextMenu(menu, v, menuInfo);
	    MenuInflater inflater = getMenuInflater();
	    inflater.inflate(R.menu.course_context_menu, menu);
	}
	
	@Override
	public boolean onContextItemSelected(MenuItem item) {
		ExpandableListView.ExpandableListContextMenuInfo info =
				(ExpandableListView.ExpandableListContextMenuInfo) item.getMenuInfo();
	    String cid = (String)info.targetView.getTag();
	    switch (item.getItemId()) {
	        case R.id.edit:
	            Toast.makeText(this, "Editting "+cid, 1000).show();
	            return true;
	        case R.id.delete:
	            Toast.makeText(this, "Deleting "+cid, 1000).show();
	            return true;
	        default:
	            return super.onContextItemSelected(item);
	    }
	}
	
	public class CourseListAdapter extends BaseExpandableListAdapter {

		public ArrayList<JSONObject> tCourses, sCourses;

		public CourseListAdapter(ArrayList<JSONObject> tCourses,
				ArrayList<JSONObject> sCourses) {
			this.tCourses = tCourses;
			this.sCourses = sCourses;
		}

		@Override
		public Object getChild(int groupPosition, int childPosition) {
			if (groupPosition==0)
				return tCourses.get(childPosition);
			else return sCourses.get(childPosition);
		}

		@Override
		public long getChildId(int groupPosition, int childPosition) {
			// TODO Auto-generated method stub
			return 0;
		}

		@Override
		public View getChildView(int groupPosition, int childPosition,
				boolean isLastChild, View convertView, ViewGroup parent) {
			View v = convertView;
			if (v == null) {
				LayoutInflater vi = (LayoutInflater) getSystemService(Context.LAYOUT_INFLATER_SERVICE);
				v = vi.inflate(R.layout.courselist_row, null);
			}

			TextView name = (TextView) v.findViewById(R.id.course_name);
			TextView desc = (TextView) v.findViewById(R.id.course_desc);
			final JSONObject course;
			if (groupPosition==0)
				course = tCourses.get(childPosition);
			else 
				course = sCourses.get(childPosition);
			try {
				name.setText(course.getString("name"));
				desc.setText(course.getString("description"));
				v.setTag(course.getString("id"));
			} catch (JSONException e) {
			}
			return v;
		}

		
		@Override
		public int getChildrenCount(int groupPosition) {
			if (groupPosition==0)
				return tCourses.size();
			else
				return sCourses.size();
		}

		@Override
		public Object getGroup(int groupPosition) {
			// TODO Auto-generated method stub
			return null;
		}

		@Override
		public int getGroupCount() {
			return 2;
		}

		@Override
		public long getGroupId(int groupPosition) {
			// TODO Auto-generated method stub
			return 0;
		}

		@Override
		public View getGroupView(int groupPosition, boolean isExpanded,
				View convertView, ViewGroup parent) {
			View v = convertView;
			if (v == null) {
				LayoutInflater vi = (LayoutInflater) getSystemService(Context.LAYOUT_INFLATER_SERVICE);
				v = vi.inflate(R.layout.group_row, null);
			}
			TextView textView = (TextView)v.findViewById(R.id.group_title);
            if (groupPosition==0)
            	textView.setText(getText(R.string.teaching));
            else
            	textView.setText(getText(R.string.studying));
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

}
