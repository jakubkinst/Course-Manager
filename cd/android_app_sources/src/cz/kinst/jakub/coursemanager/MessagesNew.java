package cz.kinst.jakub.coursemanager;

import java.util.ArrayList;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;

import android.os.AsyncTask;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;

/**
 * Activity with a form for composing and sending new Message
 * 
 * @author Jakub Kinst
 * 
 */
public class MessagesNew extends CMActivity {

	/**
	 * UID for serialization
	 */
	private static final long serialVersionUID = -3021944421553515535L;

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.messages_new);
		setProgressBarIndeterminateVisibility(false);
		final EditText subject = (EditText) findViewById(R.id.subject);
		final EditText to = (EditText) findViewById(R.id.to);
		final EditText content = (EditText) findViewById(R.id.content);
		if (getIntent().getExtras().containsKey("subject")) {
			subject.setText(getIntent().getExtras().getString("subject"));
		}
		if (getIntent().getExtras().containsKey("to")) {
			to.setText(getIntent().getExtras().getString("to"));
		}
		if (getIntent().getExtras().containsKey("content")) {
			content.setText(getIntent().getExtras().getString("content"));
		}
		Button sendButton = (Button) findViewById(R.id.sendButton);
		sendButton.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				sendMessage(subject.getText().toString(), to.getText()
						.toString(), content.getText().toString());
			}
		});
	}

	/**
	 * Submits web-form for sending a new message
	 * 
	 * @param subject
	 *            Message Subject
	 * @param to
	 *            Message Recipient
	 * @param content
	 *            Message Content
	 */
	protected void sendMessage(String subject, String to, String content) {
		final ArrayList<NameValuePair> postArgs = new ArrayList<NameValuePair>();
		postArgs.add(new BasicNameValuePair("subject", subject));
		postArgs.add(new BasicNameValuePair("to", to));
		postArgs.add(new BasicNameValuePair("content", content));
		final ArrayList<NameValuePair> getArgs = new ArrayList<NameValuePair>();

		// send message in safe thread
		new AsyncTask<Void, Void, Void>() {
			@Override
			protected void onPreExecute() {
				setProgressBarIndeterminateVisibility(true);
			};

			@Override
			protected Void doInBackground(Void... params) {
				courseManagerCon.sendForm("message", "new", "newMessage",
						getArgs, postArgs);
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

}
