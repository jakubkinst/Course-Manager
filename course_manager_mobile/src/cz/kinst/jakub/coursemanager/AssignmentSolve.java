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
import android.os.Bundle;
import android.text.Editable;
import android.text.InputType;
import android.text.TextWatcher;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup.LayoutParams;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.RadioGroup.OnCheckedChangeListener;
import android.widget.TextView;
import android.widget.Toast;
import cz.kinst.jakub.coursemanager.utils.QuestionTag;
import cz.kinst.jakub.coursemanager.utils.Utils;

public class AssignmentSolve extends CMActivity {

	protected static final int SELECT_FILE = 0;
	private int aid;
	ArrayList<QuestionTag> tags = new ArrayList<QuestionTag>();

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

		final LinearLayout assignment = (LinearLayout) findViewById(R.id.assignment);
		assignment.removeAllViews();

		for (final JSONObject question : questions) {
			String type = question.getString("type");
			final String id = question.getString("id");

			if (type.equals("text")) {
				TextView label = new TextView(this);
				label.setText(question.getString("label"));
				EditText edit = new EditText(this);
				final QuestionTag tag = new QuestionTag(id, type, "");
				tags.add(tag);
				edit.setLayoutParams(new LayoutParams(LayoutParams.FILL_PARENT,
						LayoutParams.WRAP_CONTENT));
				edit.setInputType(InputType.TYPE_TEXT_FLAG_MULTI_LINE);
				edit.setSingleLine(false);
				edit.addTextChangedListener(new TextWatcher() {
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
						tag.setValue(s.toString());
					}
				});
				assignment.addView(label);
				assignment.addView(edit);
			}
			if (type.equals("textarea")) {
				TextView label = new TextView(this);
				label.setText(question.getString("label"));
				EditText edit = new EditText(this);
				final QuestionTag tag = new QuestionTag(id, type, "");
				tags.add(tag);
				edit.setLayoutParams(new LayoutParams(LayoutParams.FILL_PARENT,
						LayoutParams.WRAP_CONTENT));
				edit.setInputType(InputType.TYPE_TEXT_FLAG_MULTI_LINE);
				edit.setSingleLine(false);
				edit.addTextChangedListener(new TextWatcher() {
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
						tag.setValue(s.toString());
					}
				});
				assignment.addView(label);
				assignment.addView(edit);
			}
			if (type.equals("radio")) {
				TextView label = new TextView(this);
				label.setText(question.getString("label"));
				RadioGroup radioGroup = new RadioGroup(this);
				final QuestionTag tag = new QuestionTag(id, type, "");
				tags.add(tag);
				final String[] choices = question.getString("choices").split(
						"#");
				for (String choice : choices) {
					RadioButton radio = new RadioButton(this);
					radio.setText(choice);
					radioGroup.addView(radio);
				}
				radioGroup
						.setOnCheckedChangeListener(new OnCheckedChangeListener() {
							@Override
							public void onCheckedChanged(RadioGroup group,
									int checkedId) {
								tag.setValue(((RadioButton) group
										.findViewById(checkedId)).getText()
										.toString());
							}
						});
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
				final QuestionTag tag = new QuestionTag(id, type, new String[0]);
				tags.add(tag);

				final OnMultiChoiceClickListener listener = new OnMultiChoiceClickListener() {
					@Override
					public void onClick(DialogInterface dialog, int which,
							boolean isChecked) {
						selected[which] = isChecked;
						tag.setValues(Utils.maskArray(choices, selected));
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
				final QuestionTag tag = new QuestionTag(id, type, "");
				tags.add(tag);
				Button pickButton = new Button(this);
				pickButton.setText(R.string.select_file);
				pickButton.setOnClickListener(new OnClickListener() {
					@Override
					public void onClick(View v) {
						Intent intent = new Intent(Intent.ACTION_GET_CONTENT);
						intent.setType("file/*");
						startActivityForResult(intent, Integer.parseInt(id));
					}
				});
				assignment.addView(label);
				assignment.addView(pickButton);
			}
		}
		Button submitButton = new Button(this);
		submitButton.setText(R.string.submit);
		submitButton.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				for (QuestionTag tag : tags) {
					if (tag.getType().equals("text"))
						Log.e("test", tag.getId() + ":" + tag.getValue());
					if (tag.getType().equals("textarea"))
						Log.e("test", tag.getId() + ":" + tag.getValue());
					if (tag.getType().equals("radio"))
						Log.e("test", tag.getId() + ":" + tag.getValue());
					if (tag.getType().equals("file"))
							Log.e("test", tag.getId() + ":" + tag.getValue());
					if (tag.getType().equals("multi"))
						Log.e("test",
								tag.getId() + ":"
										+ Utils.implode(tag.getValues(), "#"));
				}
			}
		});
		assignment.addView(submitButton);

	}

	public String getSelectedRadioValue(RadioGroup g) {

		// Returns an integer which represents the selected radio button's ID
		int selected = g.getCheckedRadioButtonId();

		// Gets a reference to our "selected" radio button
		RadioButton b = (RadioButton) findViewById(selected);

		// Now you can get the text or whatever you want from the "selected"
		// radio button
		return b.getText().toString();
	}

	@Override
	public void onActivityResult(int requestCode, int resultCode, Intent data) {
		super.onActivityResult(requestCode, resultCode, data);
		if (resultCode == Activity.RESULT_OK) {
			String filePath = data.getData().getPath();
			Log.e("test", filePath);
			for (QuestionTag tag : tags) {
				if (tag.getId().equals(String.valueOf(requestCode)))
					tag.setValue(filePath);
			}
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
