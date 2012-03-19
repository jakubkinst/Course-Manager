package cz.kinst.jakub.coursemanager;

import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.preference.Preference;
import android.preference.Preference.OnPreferenceClickListener;
import android.preference.PreferenceActivity;
import android.preference.PreferenceManager;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.EditText;

public class Settings extends PreferenceActivity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		// TODO Auto-generated method stub
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
	}

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
							public void onClick(DialogInterface dialog,
									int whichButton) {

							}
						}).create().show();
	}
}
