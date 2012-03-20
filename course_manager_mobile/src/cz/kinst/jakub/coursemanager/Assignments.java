package cz.kinst.jakub.coursemanager;

import java.text.DateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ListView;
import android.widget.TextView;
import cz.kinst.jakub.coursemanager.utils.Utils;

public class Assignments extends CMActivity {

	/**
	 * UID for serialization
	 */
	private static final long serialVersionUID = 6875506048845321547L;
	private int cid;
	public int MENU_NEW_TOPIC;

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		this.cid = getIntent().getExtras().getInt("cid");
		setContentView(R.layout.assignments);
		reload();
	}

	@Override
	protected JSONObject reloadWork() throws JSONException {
		ArrayList<NameValuePair> args = new ArrayList<NameValuePair>();
		args.add(new BasicNameValuePair("cid", String.valueOf(this.cid)));
		return courseManagerCon.getAction("assignment", "homepage", args,
				new ArrayList<NameValuePair>());
	}

	@Override
	public void gotData(JSONObject data) throws JSONException {

		JSONObject course = data.getJSONObject("activeCourse");
		setTitle(course.getString("name")+" > "+getText(R.string.assignments));
		((ListView) (findViewById(R.id.assignments)))
				.setAdapter(new AssignmentsAdapter(this,
						R.layout.assignment_row, Utils.getJSONObjectArray(data
								.getJSONArray("assignments"))));
	}

	public class AssignmentsAdapter extends ArrayAdapter<JSONObject> {

		public AssignmentsAdapter(Context context, int textViewResourceId,
				List<JSONObject> objects) {
			super(context, textViewResourceId, objects);
		}

		@Override
		public View getView(int position, View convertView, ViewGroup parent) {
			View v = convertView;
			if (v == null) {
				LayoutInflater vi = (LayoutInflater) getSystemService(Context.LAYOUT_INFLATER_SERVICE);
				v = vi.inflate(R.layout.assignment_row, null);
			}
			final JSONObject assignment = getItem(position);
			try {
				TextView name = (TextView) v.findViewById(R.id.name);
				TextView date = (TextView) v.findViewById(R.id.date);
				name.setText(assignment.getString("name"));
				Date assignDate = Utils.getDateFromDBString(assignment
						.getString("assigndate"));
				Date dueDate = Utils.getDateFromDBString(assignment
						.getString("duedate"));
				DateFormat df = DateFormat.getInstance();
				date.setText(df.format(assignDate) + " - " + df.format(dueDate));

			} catch (JSONException e) {
				e.printStackTrace();
			}

			v.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					Intent i = new Intent(Assignments.this,
							AssignmentDetail.class);
					try {
						i.putExtra("aid", assignment.getInt("id"));
					} catch (JSONException e) {
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
