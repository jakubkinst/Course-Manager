package cz.kinst.jakub.coursemanager;

import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.preference.Preference;
import android.preference.Preference.OnPreferenceClickListener;
import android.preference.PreferenceActivity;
import android.preference.PreferenceManager;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.EditText;

/**
 * Settings Activity extends standard Android PreferenceActivity Loads settings
 * structure from XML resource
 * 
 * @author Jakub Kinst
 * 
 */
public class Settings extends PreferenceActivity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		addPreferencesFromResource(R.xml.settings);

		findPreference("login").setOnPreferenceClickListener(
				new OnPreferenceClickListener() {
					@Override
					public boolean onPreferenceClick(Preference preference) {
						showLoginDialog();
						return true;
					}
				});
		findPreference("about").setOnPreferenceClickListener(
				new OnPreferenceClickListener() {
					@Override
					public boolean onPreferenceClick(Preference preference) {
						/* Create the Intent */
						final Intent emailIntent = new Intent(
								android.content.Intent.ACTION_SEND);

						/* Fill it with Data */
						emailIntent.setType("plain/text");
						emailIntent
								.putExtra(
										android.content.Intent.EXTRA_EMAIL,
										new String[] { (String) getText(R.string.app_author_email) });
						emailIntent.putExtra(
								android.content.Intent.EXTRA_SUBJECT,
								getText(R.string.app_name));

						/* Send it off to the Activity-Chooser */
						Settings.this.startActivity(Intent.createChooser(
								emailIntent, getText(R.string.send_mail)));
						return true;
					}
				});
	}

	/**
	 * Shows a dialog asking for login credentials
	 */
	private void showLoginDialog() {
		LayoutInflater factory = LayoutInflater.from(this);
		final View textEntryView = factory.inflate(R.layout.dialog_login, null);
		SharedPreferences prefs = PreferenceManager
				.getDefaultSharedPreferences(Settings.this);
		((EditText) textEntryView.findViewById(R.id.login_email)).setText(prefs
				.getString("email", ""));
		((EditText) textEntryView.findViewById(R.id.login_password))
				.setText(prefs.getString("password", ""));
		new AlertDialog.Builder(this)
				.setTitle(R.string.login)
				.setView(textEntryView)
				.setPositiveButton(R.string.login_dialog_ok,
						new DialogInterface.OnClickListener() {
							@Override
							public void onClick(DialogInterface dialog,
									int whichButton) {
								EditText email = (EditText) textEntryView
										.findViewById(R.id.login_email);
								EditText password = (EditText) textEntryView
										.findViewById(R.id.login_password);
								SharedPreferences prefs = PreferenceManager
										.getDefaultSharedPreferences(Settings.this);
								prefs.edit()
										.putString("email",
												email.getText().toString())
										.commit();
								prefs.edit()
										.putString("password",
												password.getText().toString())
										.commit();
							}
						})
				.setNegativeButton(R.string.login_dialog_cancel,
						new DialogInterface.OnClickListener() {
							@Override
							public void onClick(DialogInterface dialog,
									int whichButton) {

							}
						}).create().show();
	}
}
