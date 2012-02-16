/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package cz.kinst.jakub.coursemanager;

import java.io.File;
import java.util.ArrayList;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import android.content.SharedPreferences;
import android.content.SharedPreferences.OnSharedPreferenceChangeListener;
import android.preference.PreferenceManager;
import android.widget.Toast;
import cz.kinst.jakub.netteconnector.FlashMessage;
import cz.kinst.jakub.netteconnector.NetteConnector;

public class CourseManagerConnector extends NetteConnector implements
		OnSharedPreferenceChangeListener {

	public static String LOGTAG = "coursemanager";
	

	CMActivity context;
	public boolean logged = false;
	private boolean loggingIn = false;

	public CourseManagerConnector(String url, CMActivity context) {
		super(url);
		this.context = context;
		PreferenceManager.getDefaultSharedPreferences(context)
				.registerOnSharedPreferenceChangeListener(this);
	}
	
	public void setContext(CMActivity context){
		this.context= context;
	}

	@Override
	public JSONObject getAction(String presenter, String action,
			ArrayList<NameValuePair> getArgs, ArrayList<NameValuePair> postArgs) {
		if (!logged && !loggingIn) {			
			if (!login())
				return new JSONObject();
		}

		JSONObject data = super.getAction(presenter, action, getArgs, postArgs);
		// if empty - toast error
		if (data.length() < 1)
			this.getFlashMessages()
					.add(new FlashMessage(
							"Server Error - No Response (bad server?)", "error"));
		return data;
	}

	public boolean login() {
		this.parser.getCookies().clear();
		this.getFlashMessages().clear();
		SharedPreferences prefs = PreferenceManager
				.getDefaultSharedPreferences(context);
		String email = prefs.getString("email", "");
		String password = prefs.getString("password", "");
		ArrayList<NameValuePair> loginCred = new ArrayList<NameValuePair>();
		loginCred.add(new BasicNameValuePair("email", email));
		loginCred.add(new BasicNameValuePair("password", password));
		// login
		this.loggingIn = true;
		JSONObject result = this.sendForm("courselist", "homepage",
				"signInForm", loginCred);
		this.loggingIn = false;
		boolean l = false;
		try {
			l = result.getBoolean("logged");
		} catch (JSONException e) {
		}
		logged = l;
		return l;
	}

	public void toastFlashes() {
		for (FlashMessage flash : this.getFlashMessages()) {
			Toast.makeText(this.context, flash.getMessage(), Toast.LENGTH_SHORT)
					.show();
		}
	}

	public File getResource(int rid,String filename){
		ArrayList<NameValuePair> get = new ArrayList<NameValuePair>();
		get.add(new BasicNameValuePair("rid", String.valueOf(rid)));
		return parser.downloadFile(getUrl()+"/resource/download",get , new ArrayList<NameValuePair>(), filename);
	}
	
	@Override
	public void onSharedPreferenceChanged(SharedPreferences sharedPreferences,
			String key) {
		if (key.equals("server")) {
			this.setUrl(sharedPreferences.getString("server", ""));
			this.logged = false;
			context.reload();
		}
		if (key.equals("email") || key.equals("password")) {
			this.logged = false;
			context.reload();
		}
	}
}
