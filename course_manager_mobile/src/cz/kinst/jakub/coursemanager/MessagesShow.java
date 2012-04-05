package cz.kinst.jakub.coursemanager;

import java.util.ArrayList;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import android.content.Intent;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.widget.TextView;

/**
 * Activity shows message detail - content
 * 
 * Provides option to reply to the message
 * 
 * @author Jakub Kinst
 * 
 */
public class MessagesShow extends CMActivity {

	/**
	 * UID for serialization
	 */
	private static final long serialVersionUID = -7729764807510013237L;
	// Menu constants
	private static final int MENU_REPLY = 0;

	/**
	 * Pre-filled Message coming in intent - used for reply purposes JSON
	 * format, but String type because of easy Serialization
	 */
	private String message;

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.messages_show);
		try {
			this.message = getIntent().getExtras().getString("message");
			final JSONObject message = new JSONObject(this.message);
			JSONObject from = message.getJSONObject("from");
			JSONObject to = message.getJSONObject("to");
			((TextView) (findViewById(R.id.content))).setText(message
					.getString("content"));
			((TextView) (findViewById(R.id.from)))
					.setText(getText(R.string.from) + ": "
							+ from.getString("firstname") + " "
							+ from.getString("lastname") + " ("
							+ from.getString("email") + ")");
			((TextView) (findViewById(R.id.to))).setText(getText(R.string.to)
					+ ": " + to.getString("firstname") + " "
					+ to.getString("lastname") + " (" + to.getString("email")
					+ ")");
			((TextView) (findViewById(R.id.date))).setText(message
					.getString("sent"));
			((TextView) (findViewById(R.id.subject))).setText(message
					.getString("subject"));

			// just for set-read
			Thread t = new Thread(new Runnable() {
				@Override
				public void run() {
					ArrayList<NameValuePair> getArgs = new ArrayList<NameValuePair>();
					try {
						getArgs.add(new BasicNameValuePair("mid", message
								.getString("id")));
					} catch (JSONException e) {
					}
					courseManagerCon.getAction("message", "show-message",
							getArgs, null);
				}
			});
			t.start();

		} catch (JSONException e) {
			e.printStackTrace();
		}
		setProgressBarIndeterminateVisibility(false);
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		boolean result = super.onCreateOptionsMenu(menu);

		// Add reply button to menu
		MenuItem newComment = menu.add(0, MENU_REPLY, 0, R.string.reply);
		newComment.setIcon(R.drawable.ic_action_reply);
		if (Integer.valueOf(android.os.Build.VERSION.SDK) >= 11) {
			newComment.setShowAsAction(MenuItem.SHOW_AS_ACTION_ALWAYS);
		}
		return result;
	}

	@Override
	public boolean onMenuItemSelected(int featureId, MenuItem item) {
		if (item.getItemId() == MENU_REPLY) {
			try {

				JSONObject message = new JSONObject(this.message);
				startActivity(new Intent(this, MessagesNew.class)
						.putExtra("cm", courseManagerCon)
						.putExtra(
								"to",
								message.getJSONObject("from")
										.getString("email"))
						.putExtra("subject",
								"Re:" + message.getString("subject"))
						.putExtra("content",
								wrapReply(message.getString("content"))));
			} catch (JSONException e) {
				e.printStackTrace();
			}
			return true;
		} else {
			return super.onMenuItemSelected(featureId, item);
		}
	}

	/**
	 * Inserts > char in front of each line of passed string
	 * 
	 * @param msg
	 *            String to modify
	 * @return
	 */
	public static String wrapReply(String msg) {
		return ">" + msg.replace("\n", "\n" + ">");
	}

}
