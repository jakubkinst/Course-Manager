package cz.kinst.jakub.coursemanager;

import java.util.ArrayList;
import java.util.Date;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.DialogInterface.OnMultiChoiceClickListener;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.text.InputType;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup.LayoutParams;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;
import cz.kinst.jakub.coursemanager.utils.Utils;

public class AssignmentSolve extends CMActivity {

	protected static final int SELECT_FILE = 0;
	private int aid;

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		this.aid = getIntent().getExtras().getInt("aid");
		setContentView(R.layout.assignment_solve);
		reload();
	}

	@Override
	protected JSONObject reloadWork() throws JSONException {
		ArrayList<NameValuePair> args = new ArrayList<NameValuePair>();
		args.add(new BasicNameValuePair("aid", String.valueOf(this.aid)));
		return courseManagerCon.getAction("assignment", "solve", args,
				new ArrayList<NameValuePair>());
	}

	@Override
	public void gotData(JSONObject data) throws JSONException {
		ArrayList<JSONObject> questions = Utils.getJSONObjectArray(data
				.getJSONArray("questions"));
		Date realEndTime = Utils.getDateFromDBString(data
				.getString("realEndTime"));

		LinearLayout assignment = (LinearLayout) findViewById(R.id.assignment);
		assignment.removeAllViews();

		for (final JSONObject question : questions) {
			String type = question.getString("type");
			String id = question.getString("id");

			if (type.equals("text")) {
				TextView label = new TextView(this);
				label.setText(question.getString("label"));
				EditText edit = new EditText(this);
				edit.setTag(id);
				edit.setLayoutParams(new LayoutParams(LayoutParams.FILL_PARENT,
						LayoutParams.WRAP_CONTENT));
				edit.setInputType(InputType.TYPE_TEXT_FLAG_MULTI_LINE);
				edit.setSingleLine(false);
				assignment.addView(label);
				assignment.addView(edit);
			}
			if (type.equals("textarea")) {
				TextView label = new TextView(this);
				label.setText(question.getString("label"));
				EditText edit = new EditText(this);
				edit.setTag(id);
				edit.setLayoutParams(new LayoutParams(LayoutParams.FILL_PARENT,
						LayoutParams.WRAP_CONTENT));
				edit.setInputType(InputType.TYPE_TEXT_FLAG_MULTI_LINE);
				edit.setSingleLine(false);
				assignment.addView(label);
				assignment.addView(edit);
			}
			if (type.equals("radio")) {
				TextView label = new TextView(this);
				label.setText(question.getString("label"));
				RadioGroup radioGroup = new RadioGroup(this);
				for (String choice : question.getString("choices").split("#")) {
					RadioButton radio = new RadioButton(this);
					radio.setText(choice);
					radioGroup.addView(radio);
				}
				assignment.addView(label);
				assignment.addView(radioGroup);
			}
			if (type.equals("multi")) {
				final String[] choices = question.getString("choices").split(
						"#");
				TextView label = new TextView(this);
				label.setText(question.getString("label"));
				final boolean[] selected = new boolean[choices.length];
				for (boolean b : selected)
					b = false;

				final Button select = new Button(this);

				final OnMultiChoiceClickListener listener = new OnMultiChoiceClickListener() {
					@Override
					public void onClick(DialogInterface dialog, int which,
							boolean isChecked) {
						selected[which] = isChecked;
						select.setText(getText(R.string.selected) + ": "
								+ String.valueOf(countSelected(selected)));
					}
				};

				select.setText(getText(R.string.selected) + ": "
						+ getText(R.string.nothing));

				select.setOnClickListener(new OnClickListener() {

					@Override
					public void onClick(View v) {
						AlertDialog.Builder builder = new AlertDialog.Builder(
								AssignmentSolve.this);
						try {
							builder.setTitle(question.getString("label"));
							builder.setMultiChoiceItems(choices, selected,
									listener);
						} catch (JSONException e) {
							e.printStackTrace();
						}
						AlertDialog alert = builder.create();

						alert.show();
					}
				});
				assignment.addView(label);
				assignment.addView(select);
			}
			if (type.equals("file")) {
				TextView label = new TextView(this);
				label.setText(question.getString("label"));
				Button pickButton = new Button(this);
				pickButton.setText(R.string.select_file);
				pickButton.setOnClickListener(new OnClickListener() {
					@Override
					public void onClick(View v) {
						Intent intent = new Intent(Intent.ACTION_GET_CONTENT);
						intent.setType("file/*");
						startActivityForResult(intent, SELECT_FILE);
					}
				});
				assignment.addView(label);
				assignment.addView(pickButton);
			}
		}

	}

	@Override
	public void onActivityResult(int requestCode, int resultCode, Intent data) {
		super.onActivityResult(requestCode, resultCode, data);
		if (requestCode == SELECT_FILE)
			if (resultCode == Activity.RESULT_OK) {
				String filePath = data.getData().getPath();
				// TODO Do something with the select image URI
				Toast.makeText(this, filePath, 1000).show();
			}
	}

	public int countSelected(boolean[] array) {
		int n = 0;
		for (boolean b : array)
			if (b)
				n++;
		return n;
	}
}
