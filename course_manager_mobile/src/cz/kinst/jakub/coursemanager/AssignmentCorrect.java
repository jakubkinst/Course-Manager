package cz.kinst.jakub.coursemanager;

import java.io.File;
import java.util.ArrayList;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import android.content.Intent;
import android.graphics.Typeface;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Environment;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.Gravity;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup.LayoutParams;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TableLayout;
import android.widget.TableRow;
import android.widget.TextView;
import android.widget.Toast;
import cz.kinst.jakub.coursemanager.utils.Utils;

/**
 * 
 * Activity providing interface for course assignment correction. This activity
 * will be available only for teachers of the course.
 * 
 * @author Jakub Kinst
 * 
 */
public class AssignmentCorrect extends CMActivity {

	/**
	 * UID for serialization
	 */
	private static final long serialVersionUID = -6087997793622768354L;

	/**
	 * Assignment ID
	 */
	private int aid;

	/**
	 * Array structure for storing new results from input
	 */
	private ArrayList<UserResult> results = new ArrayList<UserResult>();

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		// get Assignment ID from the intent
		this.aid = getIntent().getExtras().getInt("aid");
		setContentView(R.layout.assignment_correct);
		reload();
	}

	@Override
	protected JSONObject reloadWork() throws JSONException {
		ArrayList<NameValuePair> args = new ArrayList<NameValuePair>();
		args.add(new BasicNameValuePair("aid", String.valueOf(this.aid)));
		return courseManagerCon.getAction("assignment", "correct", args,
				new ArrayList<NameValuePair>());
	}

	@Override
	public void gotData(JSONObject data) throws JSONException {
		// get data from JSON
		JSONObject course = data.getJSONObject("activeCourse");
		setTitle(course.getString("name") + " > "
				+ getText(R.string.assignment) + " > "
				+ getText(R.string.correct));
		drawCorrectTable(data);

		// correction save button stuff
		Button correctButton = (Button) findViewById(R.id.correctButton);
		correctButton.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				final ArrayList<NameValuePair> postArgs = new ArrayList<NameValuePair>();
				final ArrayList<NameValuePair> getArgs = new ArrayList<NameValuePair>();
				getArgs.add(new BasicNameValuePair("aid", String.valueOf(aid)));
				for (UserResult result : results) {
					postArgs.add(new BasicNameValuePair(result.getId() + "_",
							result.getResult()));
				}

				// Do http request in AsyncTask
				new AsyncTask<Void, Void, Void>() {
					@Override
					protected void onPreExecute() {
						setProgressBarIndeterminateVisibility(true);
					};

					@Override
					protected Void doInBackground(Void... params) {
						// send the form with data
						courseManagerCon.sendForm("assignment", "correct",
								"correctForm", getArgs, postArgs);
						return null;
					}

					@Override
					protected void onPostExecute(Void result) {
						setProgressBarIndeterminateVisibility(false);
						courseManagerCon.toastFlashes();
						finish();
					};
				}.execute();
			}
		});
	}

	/**
	 * Draws result to UI in form of a table
	 * 
	 * @param data
	 *            JSON data from HTTP response
	 * @throws JSONException
	 */
	private void drawCorrectTable(JSONObject data) throws JSONException {
		ArrayList<JSONObject> submissions = Utils.getJSONObjectArray(data
				.getJSONArray("submissions"));
		ArrayList<JSONObject> questions = Utils.getJSONObjectArray(data
				.getJSONArray("questions"));

		TableLayout table = (TableLayout) findViewById(R.id.answers);
		table.removeAllViews();

		// table head
		TableRow th = new TableRow(this);
		TextView nameTitle = new TextView(this);
		nameTitle.setText(R.string.name);
		nameTitle.setPadding(3, 3, 3, 3);
		nameTitle.setTypeface(null, Typeface.BOLD);
		nameTitle.setGravity(Gravity.CENTER);
		th.addView(nameTitle);
		for (JSONObject question : questions) {
			TextView title = new TextView(this);
			title.setText(question.getString("label"));
			title.setTypeface(null, Typeface.BOLD);
			title.setPadding(3, 3, 3, 3);
			title.setGravity(Gravity.CENTER);
			th.addView(title);
		}
		TextView pointsHeader = new TextView(this);
		pointsHeader.setText(R.string.points);
		int maxpoints = data.getJSONObject("assignment").getInt("maxpoints");
		if (maxpoints > 0) {
			pointsHeader.setText(pointsHeader.getText() + "(max: " + maxpoints
					+ ")");
		}
		pointsHeader.setPadding(3, 3, 3, 3);
		pointsHeader.setTypeface(null, Typeface.BOLD);
		pointsHeader.setGravity(Gravity.CENTER);
		th.addView(pointsHeader);

		table.addView(th);

		// table body
		for (JSONObject submission : submissions) {
			TableRow tr = new TableRow(this);
			tr.setLayoutParams(new LayoutParams(LayoutParams.FILL_PARENT,
					LayoutParams.WRAP_CONTENT));
			TextView name = new TextView(this);
			JSONObject user = submission.getJSONObject("user");
			name.setText(user.getString("firstname") + " "
					+ user.getString("lastname"));
			name.setTypeface(null, Typeface.ITALIC);
			tr.addView(name);

			for (JSONObject question : questions) {
				if (question.getString("type").equals("file")
						&& submission.has(question.getString("id"))) {
					Button dl = new Button(this);
					final int resourceId = submission.getInt(question
							.getString("id"));
					final String courseId = data.getJSONObject("assignment")
							.getString("Course_id");
					final String filename = "file."
							+ data.getJSONObject("extensions").getString(
									String.valueOf(resourceId));
					dl.setText(R.string.download);
					dl.setOnClickListener(new OnClickListener() {
						@Override
						public void onClick(View v) {
							new AsyncTask<Void, Void, File>() {

								@Override
								protected void onPreExecute() {
									setProgressBarIndeterminateVisibility(true);
									Toast.makeText(AssignmentCorrect.this,
											R.string.download_started, 2000)
											.show();
								}

								@Override
								protected File doInBackground(Void... resource) {
									File myFolder = new File(Environment
											.getExternalStorageDirectory()
											+ "/CourseManager_downloads");
									myFolder.mkdirs();
									File file = new File("");
									file = courseManagerCon
											.getAssignmentResource(resourceId,
													myFolder + "/" + filename,
													courseId);
									return file;
								}

								@Override
								protected void onPostExecute(File file) {
									Toast.makeText(
											AssignmentCorrect.this,
											R.string.file_saved_to
													+ file.getAbsolutePath(),
											2000).show();
									try {
										Intent intent = new Intent();
										intent.setAction(android.content.Intent.ACTION_VIEW);
										intent.setDataAndType(
												Uri.fromFile(file),
												Utils.getMIMEType(file));
										startActivity(intent);
									} catch (Exception e) {
										Toast.makeText(AssignmentCorrect.this,
												R.string.no_application_found,
												2000).show();
									}
									setProgressBarIndeterminateVisibility(false);

								}
							}.execute();
						};
					});

					tr.addView(dl);

				} else {
					TextView answer = new TextView(this);
					String res;
					if (submission.has(question.getString("id"))) {
						res = submission.getString(question.getString("id"));
					} else {
						res = "null";
					}
					if (question.getString("type").equals("multi")) {
						res = res.replace("#", "\n");
					}
					answer.setText(!res.equals("null") ? res : "");
					answer.setPadding(3, 3, 3, 3);
					answer.setGravity(Gravity.CENTER);
					tr.addView(answer);
				}
			}

			final UserResult result = new UserResult(user.getString("id"), "");
			results.add(result);
			EditText pts = new EditText(this);
			pts.addTextChangedListener(new TextWatcher() {
				@Override
				public void onTextChanged(CharSequence s, int start,
						int before, int count) {
				}

				@Override
				public void beforeTextChanged(CharSequence s, int start,
						int count, int after) {
				}

				@Override
				public void afterTextChanged(Editable s) {
					result.setResult(s.toString());
				}
			});
			pts.setPadding(3, 3, 3, 3);
			if (!submission.isNull("points")) {
				pts.setText(submission.getString("points"));
			}
			pts.setGravity(Gravity.CENTER);
			tr.addView(pts);

			table.addView(tr);
		}

	}

	/**
	 * Utility Class for storing result of particular assignment
	 * 
	 * @author Jakub Kinst
	 * 
	 */
	public class UserResult {

		/**
		 * User id
		 */
		String id;

		/**
		 * Result (pts)
		 */
		String result;

		/**
		 * New user result
		 * 
		 * @param id
		 *            User ID
		 * @param result
		 *            Result
		 */
		public UserResult(String id, String result) {
			this.id = id;
			this.result = result;
		}

		public void setId(String id) {
			this.id = id;
		}

		public void setResult(String result) {
			this.result = result;
		}

		public String getId() {
			return id;
		}

		public String getResult() {
			return result;
		}
	}

}
