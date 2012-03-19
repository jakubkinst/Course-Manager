package cz.kinst.jakub.coursemanager;

import java.util.ArrayList;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import android.graphics.Typeface;
import android.os.Bundle;
import android.view.Gravity;
import android.view.ViewGroup.LayoutParams;
import android.widget.TableLayout;
import android.widget.TableRow;
import android.widget.TextView;
import cz.kinst.jakub.coursemanager.utils.Utils;

public class Results extends CMActivity {

	/**
	 * UID for serialization
	 */
	private static final long serialVersionUID = -6087997793622768354L;
	private int cid;

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		this.cid = getIntent().getExtras().getInt("cid");
		setContentView(R.layout.results);
		reload();
	}

	@Override
	protected JSONObject reloadWork() throws JSONException {
		ArrayList<NameValuePair> args = new ArrayList<NameValuePair>();
		args.add(new BasicNameValuePair("cid", String.valueOf(this.cid)));
		return courseManagerCon.getAction("result", "homepage", args,
				new ArrayList<NameValuePair>());
	}

	@Override
	public void gotData(JSONObject data) throws JSONException {

		drawPointsTable(data);
		drawGradesTable(data);
	}

	private void drawPointsTable(JSONObject data) throws JSONException {
		ArrayList<JSONObject> students = Utils.getJSONObjectArray(data
				.getJSONArray("students"));
		ArrayList<JSONObject> points = Utils.getJSONObjectArray(data
				.getJSONArray("offlinePoints"));
		points.addAll(Utils.getJSONObjectArray(data
				.getJSONArray("onlinePoints")));

		JSONObject sums = data.getJSONObject("sums");

		TableLayout pointResults = (TableLayout) findViewById(R.id.pointResults);
		pointResults.removeAllViews();
		// table head
		TableRow th = new TableRow(this);
		TextView nameTitle = new TextView(this);
		nameTitle.setText(R.string.name);
		nameTitle.setPadding(3, 3, 3, 3);
		nameTitle.setTypeface(null, Typeface.BOLD);
		nameTitle.setGravity(Gravity.CENTER);
		th.addView(nameTitle);
		for (JSONObject task : points) {
			TextView title = new TextView(this);
			title.setText(task.getString("name"));
			title.setTypeface(null, Typeface.BOLD);
			title.setPadding(3, 3, 3, 3);
			title.setGravity(Gravity.CENTER);
			th.addView(title);
		}
		TextView sumTitle = new TextView(this);
		sumTitle.setText(R.string.sum);
		sumTitle.setPadding(3, 3, 3, 3);
		sumTitle.setTypeface(null, Typeface.BOLD);
		sumTitle.setGravity(Gravity.CENTER);
		th.addView(sumTitle);

		pointResults.addView(th);

		// table body
		for (JSONObject student : students) {
			TableRow tr = new TableRow(this);
			tr.setLayoutParams(new LayoutParams(LayoutParams.FILL_PARENT,
					LayoutParams.WRAP_CONTENT));
			TextView name = new TextView(this);
			name.setText(student.getString("firstname") + " "
					+ student.getString("lastname"));
			name.setTypeface(null, Typeface.ITALIC);
			tr.addView(name);

			for (JSONObject task : points) {
				TextView pts = new TextView(this);
				String res;
				if (task.has(student.getString("id"))) {
					res = task.getString(student.getString("id"));
				} else {
					res = "null";
				}
				pts.setText(!res.equals("null") ? res : "");
				pts.setPadding(3, 3, 3, 3);
				pts.setGravity(Gravity.CENTER);
				tr.addView(pts);
			}

			TextView pts = new TextView(this);
			if (sums.has(student.getString("id"))) {
				double sum = Double.parseDouble(sums.getString(student
						.getString("id")));
				pts.setText(String.valueOf(Utils.round(sum, 3)));
			}
			pts.setPadding(3, 3, 3, 3);
			pts.setGravity(Gravity.CENTER);
			tr.addView(pts);

			pointResults.addView(tr);
		}

	}

	private void drawGradesTable(JSONObject data) throws JSONException {

		ArrayList<JSONObject> students = Utils.getJSONObjectArray(data
				.getJSONArray("students"));
		ArrayList<JSONObject> grades = Utils.getJSONObjectArray(data
				.getJSONArray("offlineGrades"));

		JSONObject avgs = data.getJSONObject("avgs");

		TableLayout gradeResults = (TableLayout) findViewById(R.id.gradeResults);
		gradeResults.removeAllViews();
		// table head
		TableRow th = new TableRow(this);
		TextView nameTitle = new TextView(this);
		nameTitle.setText(R.string.name);
		nameTitle.setPadding(3, 3, 3, 3);
		nameTitle.setTypeface(null, Typeface.BOLD);
		nameTitle.setGravity(Gravity.CENTER);
		th.addView(nameTitle);
		for (JSONObject task : grades) {
			TextView title = new TextView(this);
			title.setText(task.getString("name"));
			title.setTypeface(null, Typeface.BOLD);
			title.setPadding(3, 3, 3, 3);
			title.setGravity(Gravity.CENTER);
			th.addView(title);
		}
		TextView sumTitle = new TextView(this);
		sumTitle.setText(R.string.average);
		sumTitle.setPadding(3, 3, 3, 3);
		sumTitle.setTypeface(null, Typeface.BOLD);
		sumTitle.setGravity(Gravity.CENTER);
		th.addView(sumTitle);

		gradeResults.addView(th);

		// table body
		for (JSONObject student : students) {
			TableRow tr = new TableRow(this);
			tr.setLayoutParams(new LayoutParams(LayoutParams.FILL_PARENT,
					LayoutParams.WRAP_CONTENT));
			TextView name = new TextView(this);
			name.setText(student.getString("firstname") + " "
					+ student.getString("lastname"));
			name.setTypeface(null, Typeface.ITALIC);
			tr.addView(name);

			for (JSONObject task : grades) {
				TextView pts = new TextView(this);
				String res;
				if (task.has(student.getString("id"))) {
					res = task.getString(student.getString("id"));
				} else {
					res = "null";
				}
				pts.setText(!res.equals("null") ? res : "");
				pts.setPadding(3, 3, 3, 3);
				pts.setGravity(Gravity.CENTER);
				tr.addView(pts);
			}

			TextView pts = new TextView(this);
			if (avgs.has(student.getString("id"))) {
				double avg = Double.parseDouble(avgs.getString(student
						.getString("id")));
				pts.setText(String.valueOf(Utils.round(avg, 3)));
			}
			pts.setPadding(3, 3, 3, 3);
			pts.setGravity(Gravity.CENTER);
			tr.addView(pts);

			gradeResults.addView(tr);
		}

	}

}
