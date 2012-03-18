package cz.kinst.jakub.netteconnector;

import java.io.File;
import java.io.Serializable;
import java.util.ArrayList;
import java.util.HashMap;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONObject;

import android.util.Log;
import cz.kinst.jakub.coursemanager.CourseManagerConnector;

/**
 * 
 * @author Jakub Kinst
 */
public class NetteConnector implements Serializable {

	/**
	 * 
	 */
	private static final long serialVersionUID = 1L;
	private String URL;
	protected HTTPSmartClient parser;
	private NetteJSONClient jsonClient;

	public String getUrl() {
		return URL;
	}

	public void setUrl(String url) {
		this.URL = url;
	}

	public NetteConnector(String url) {
		URL = url;
		parser = new HTTPSmartClient();
		jsonClient = new NetteJSONClient();
	}

	public JSONObject sendSignal(String presenter, String signal,
			ArrayList<NameValuePair> getArgs, ArrayList<NameValuePair> postArgs) {
		if (getArgs == null) {
			getArgs = new ArrayList<NameValuePair>();
		}
		getArgs.add(new BasicNameValuePair("do", signal));
		return getAction(presenter, null, getArgs, postArgs);
	}

	public JSONObject sendForm(String presenter, String action,
			String formName, ArrayList<NameValuePair> postArgs) {
		return sendForm(presenter, action, formName, null, postArgs);
	}

	public JSONObject sendForm(String presenter, String action,
			String formName, ArrayList<NameValuePair> getArgs,
			ArrayList<NameValuePair> postArgs){
		return sendForm(presenter, action, formName, getArgs, postArgs,null);
	}
	
	public JSONObject sendForm(String presenter, String action,
			String formName, ArrayList<NameValuePair> getArgs,
			ArrayList<NameValuePair> postArgs,HashMap<String, File> files) {
		if (getArgs == null)
			getArgs = new ArrayList<NameValuePair>();
		getArgs.add(new BasicNameValuePair("do", formName + "-submit"));
		return getAction(presenter, action, getArgs, postArgs,files);
	}

	public JSONObject getAction(String presenter, String action,
			ArrayList<NameValuePair> getArgs, ArrayList<NameValuePair> postArgs) {
		return getAction(presenter, action, getArgs, postArgs, null);
	}

	public JSONObject getAction(String presenter, String action,
			ArrayList<NameValuePair> getArgs,
			ArrayList<NameValuePair> postArgs, HashMap<String, File> files) {

		if (getArgs == null) {
			getArgs = new ArrayList<NameValuePair>();
		}
		String path = presenter;
		if (action != null)
			path = path + "/" + action;

		getArgs.add(new BasicNameValuePair("mobile", "1"));

		JSONObject result = new JSONObject();
		try {
			result = jsonClient.getJSONObject(parser.getJSON(URL + "/" + path,
					getArgs, postArgs, files));
		} catch (Exception e) {
			Log.e(CourseManagerConnector.LOGTAG, "++++" + e.getMessage());
		}
		return result;

	}

	public JSONObject getAction(String presenter) {
		return getAction(presenter, null, null, null);
	}

	public JSONObject getAction(String presenter, String action) {
		return getAction(presenter, action, null, null);
	}

	public ArrayList<FlashMessage> getFlashMessages() {
		return jsonClient.getFlashMessages();
	}

	public void addFlashMessage(String msg, String type) {
		jsonClient.getFlashMessages().add(new FlashMessage(msg, type));
	}
}
