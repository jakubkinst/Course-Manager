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
public class AndroidetteConnector implements Serializable {

	private static final String LOG_TAG = "Androidette_Connector";
	private static final String NETTE_MOBILE_PARAM = "mobile";
	private static final String NETTE_FORM_SIGNAL_APENDIX = "-submit";
	private static final String NETTE_SIGNAL_PREFIX = "do";
	/**
	 * UID for serialization
	 */
	private static final long serialVersionUID = -4933359977995094289L;
	private String url;
	protected HTTPSmartClient httpClient;
	private AndroidetteJSONClient jsonClient;

	public AndroidetteConnector(String url) {
		this.url = url;
		httpClient = new HTTPSmartClient();
		jsonClient = new AndroidetteJSONClient();
	}

	public String getUrl() {
		return url;
	}

	public void setUrl(String url) {
		this.url = url;
	}

	public JSONObject sendSignal(String presenter, String signal,
			ArrayList<NameValuePair> getArgs, ArrayList<NameValuePair> postArgs) {
		if (getArgs == null) {
			getArgs = new ArrayList<NameValuePair>();
		}

		getArgs.add(new BasicNameValuePair(NETTE_SIGNAL_PREFIX, signal));
		return getAction(presenter, null, getArgs, postArgs);
	}

	public JSONObject sendForm(String presenter, String action,
			String formName, ArrayList<NameValuePair> postArgs) {
		return sendForm(presenter, action, formName, null, postArgs, null);
	}

	public JSONObject sendForm(String presenter, String action,
			String formName, ArrayList<NameValuePair> getArgs,
			ArrayList<NameValuePair> postArgs) {
		return sendForm(presenter, action, formName, getArgs, postArgs, null);
	}

	public JSONObject sendForm(String presenter, String action,
			String formName, ArrayList<NameValuePair> getArgs,
			ArrayList<NameValuePair> postArgs, HashMap<String, File> files) {
		if (getArgs == null) {
			getArgs = new ArrayList<NameValuePair>();
		}

		getArgs.add(new BasicNameValuePair(NETTE_SIGNAL_PREFIX, formName
				+ NETTE_FORM_SIGNAL_APENDIX));

		return getAction(presenter, action, getArgs, postArgs, files);
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

		// build url
		String path = presenter;
		if (action != null) {
			path = path + "/" + action;
		}

		// add mobile parameter
		getArgs.add(new BasicNameValuePair(NETTE_MOBILE_PARAM, "1"));

		JSONObject result = new JSONObject();
		try {
			result = jsonClient.processStringToJSON(httpClient.getJSON(url
					+ "/" + path, getArgs, postArgs, files));
		} catch (Exception e) {
			Log.e(LOG_TAG, e.getMessage());
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
