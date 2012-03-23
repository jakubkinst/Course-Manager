/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package cz.kinst.jakub.coursemanager;

import java.io.File;
import java.util.ArrayList;
import java.util.HashMap;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import android.content.SharedPreferences;
import android.content.SharedPreferences.OnSharedPreferenceChangeListener;
import android.preference.PreferenceManager;
import android.widget.Toast;
import cz.kinst.jakub.androidette.AndroidetteConnector;
import cz.kinst.jakub.androidette.FlashMessage;

public class CourseManagerConnector extends AndroidetteConnector implements
		OnSharedPreferenceChangeListener {

	/**
	 * 
	 */
	private static final long serialVersionUID = -7545595093935513854L;

	public static String LOGTAG = "coursemanager";

	CMActivity context;
	public boolean logged = false;
	private boolean loggingIn = false;

	private String apiKey;

	public CourseManagerConnector(String url,String apiKey, CMActivity context) {
		super(url);
		this.context = context;
		this.apiKey = apiKey;
		PreferenceManager.getDefaultSharedPreferences(context)
				.registerOnSharedPreferenceChangeListener(this);
	}

	public void setContext(CMActivity context) {
		this.context = context;
	}

	@Override
	public JSONObject getAction(String presenter, String action,
			ArrayList<NameValuePair> getArgs,
			ArrayList<NameValuePair> postArgs, HashMap<String, File> files) {
		if (!logged && !loggingIn) {
			login();
			if (!logged) {
				return new JSONObject();
			}
		}

		if (getArgs == null) {
			getArgs = new ArrayList<NameValuePair>();
		}

		// add api-key to url
		if (apiKey != null) {
			getArgs.add(new BasicNameValuePair("apiKey", apiKey));
		}

		JSONObject data = super.getAction(presenter, action, getArgs, postArgs,
				files);

		// if empty - toast error
		if (data.length() < 1) {
			this.getFlashMessages()
					.add(new FlashMessage(
							"Server Error - No Response (bad server?)", "error"));
		}
		return data;
	}

	public boolean login() {
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

		try {
			logged = result.getBoolean("logged");
		} catch (JSONException e) {
			logged = false;
		}


		this.loggingIn = false;
		return logged;
	}

	
	public void toastFlashes() {
		for (FlashMessage flash : this.getFlashMessages()) {
			Toast.makeText(this.context, flash.getMessage(), Toast.LENGTH_SHORT)
					.show();
		}
	}

	public File getResource(int rid, String filename) {
		ArrayList<NameValuePair> get = new ArrayList<NameValuePair>();
		get.add(new BasicNameValuePair("rid", String.valueOf(rid)));
		return httpClient.downloadFile(getUrl() + "/resource/download", get,
				new ArrayList<NameValuePair>(), filename);
	}

	public File getAssignmentResource(int afid, String filename, String cid) {
		ArrayList<NameValuePair> get = new ArrayList<NameValuePair>();
		get.add(new BasicNameValuePair("cid", String.valueOf(cid)));
		get.add(new BasicNameValuePair("afid", String.valueOf(afid)));
		return httpClient.downloadFile(getUrl() + "/assignment/download-file",
				get, new ArrayList<NameValuePair>(), filename);
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
