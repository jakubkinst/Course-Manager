package cz.kinst.jakub.coursemanager;

import java.util.ArrayList;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;

import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;

public class MessagesNew extends CMActivity {

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.messages_new);
		setProgressBarIndeterminateVisibility(false);
		final EditText subject = (EditText) findViewById(R.id.subject);
		final EditText to = (EditText) findViewById(R.id.to);
		final EditText content = (EditText) findViewById(R.id.content);
		if (getIntent().getExtras().containsKey("subject"))
			subject.setText(getIntent().getExtras().getString("subject"));
		if (getIntent().getExtras().containsKey("to"))
			to.setText(getIntent().getExtras().getString("to"));
		if (getIntent().getExtras().containsKey("content"))
			content.setText(getIntent().getExtras().getString("content"));
		Button sendButton = (Button) findViewById(R.id.sendButton);
		sendButton.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				sendMessage(subject.getText().toString(), to.getText()
						.toString(), content.getText().toString());
			}
		});
	}

	protected void sendMessage(String subject, String to, String content) {
		final ArrayList<NameValuePair> postArgs = new ArrayList<NameValuePair>();
		postArgs.add(new BasicNameValuePair("subject", subject));
		postArgs.add(new BasicNameValuePair("to", to));
		postArgs.add(new BasicNameValuePair("content", content));
		final ArrayList<NameValuePair> getArgs = new ArrayList<NameValuePair>();

		// post topic in safe thread
		new AsyncTask<Void, Void, Void>() {
			protected void onPreExecute() {
				setProgressBarIndeterminateVisibility(true);
			};

			protected Void doInBackground(Void... params) {
				courseManagerCon.sendForm("message", "new", "newMessage",
						getArgs, postArgs);
				return null;
			}

			protected void onPostExecute(Void result) {
				setProgressBarIndeterminateVisibility(false);
				courseManagerCon.toastFlashes();
				finish();
			};
		}.execute();
	}

}
