package cz.kinst.jakub.androidette;

import java.io.File;
import java.io.Serializable;
import java.util.ArrayList;
import java.util.HashMap;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONObject;

import android.util.Log;

/**
 * Main Class providing connection to any Web Application based on Nette
 * Framework({@link http://nette.org})
 * 
 * @author Jakub Kinst
 */
public class AndroidetteConnector implements Serializable {

	private static final String LOG_TAG = "Androidette_Connector";

	/**
	 * GET parameter indicating moble connection
	 */
	private static final String NETTE_MOBILE_PARAM = "mobile";

	/**
	 * default Nette form-submit appendix
	 */
	private static final String NETTE_FORM_SIGNAL_APENDIX = "-submit";

	/**
	 * Default Nette signal prefix
	 */
	private static final String NETTE_SIGNAL_PREFIX = "do";

	/**
	 * UID for serialization
	 */
	private static final long serialVersionUID = -4933359977995094289L;

	/**
	 * URL of web-app
	 */
	private String url;

	/**
	 * Instance of HTTPSmartClient providing HTTP connection
	 */
	protected HTTPSmartClient httpClient;

	/**
	 * Instance of JSONClient providing JSON utilities
	 */
	private AndroidetteJSONClient jsonClient;

	/**
	 * Default constructor
	 * 
	 * @param url
	 *            URL of web-app
	 */
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

	/**
	 * Sends Nette signal
	 * 
	 * @param presenter
	 * @param signal
	 *            signal name
	 * @param getArgs
	 *            GET parameters
	 * @param postArgs
	 *            POST parameters
	 * @return JSONObject
	 */
	public JSONObject sendSignal(String presenter, String signal,
			ArrayList<NameValuePair> getArgs, ArrayList<NameValuePair> postArgs) {
		if (getArgs == null) {
			getArgs = new ArrayList<NameValuePair>();
		}

		getArgs.add(new BasicNameValuePair(NETTE_SIGNAL_PREFIX, signal));
		return getAction(presenter, null, getArgs, postArgs);
	}

	/**
	 * Submits Nette Web Form. Values must be provided in postArgs
	 * 
	 * @param presenter
	 * @param action
	 * @param formName
	 * @param postArgs
	 * @return
	 */
	public JSONObject sendForm(String presenter, String action,
			String formName, ArrayList<NameValuePair> postArgs) {
		return sendForm(presenter, action, formName, null, postArgs, null);
	}

	/**
	 * Submits Nette Web Form. Values must be provided in postArgs
	 * 
	 * @param presenter
	 * @param action
	 * @param formName
	 * @param getArgs
	 * @param postArgs
	 * @return
	 */
	public JSONObject sendForm(String presenter, String action,
			String formName, ArrayList<NameValuePair> getArgs,
			ArrayList<NameValuePair> postArgs) {
		return sendForm(presenter, action, formName, getArgs, postArgs, null);
	}

	/**
	 * Submits Nette Web Form. Values must be provided in postArgs
	 * 
	 * @param presenter
	 * @param action
	 * @param formName
	 * @param getArgs
	 * @param postArgs
	 * @param files
	 * @return
	 */
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

	/**
	 * Returns content of an action of a specific presenter
	 * 
	 * @param presenter
	 * @param action
	 * @param getArgs
	 * @param postArgs
	 * @return
	 */
	public JSONObject getAction(String presenter, String action,
			ArrayList<NameValuePair> getArgs, ArrayList<NameValuePair> postArgs) {
		return getAction(presenter, action, getArgs, postArgs, null);
	}

	/**
	 * Returns content of an action of a specific presenter
	 * 
	 * @param presenter
	 * @param action
	 * @param getArgs
	 * @param postArgs
	 * @param files
	 * @return
	 */
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

	/**
	 * Returns content of an action of a specific presenter Default action will
	 * be selected by server router
	 * 
	 * @param presenter
	 * @return
	 */
	public JSONObject getAction(String presenter) {
		return getAction(presenter, null, null, null);
	}

	/**
	 * Returns content of an action of a specific presenter
	 * 
	 * @param presenter
	 * @param action
	 * @return
	 */
	public JSONObject getAction(String presenter, String action) {
		return getAction(presenter, action, null, null);
	}

	/**
	 * Getter for loaded flash messages
	 * 
	 * @return
	 */
	public ArrayList<FlashMessage> getFlashMessages() {
		return jsonClient.getFlashMessages();
	}

	/**
	 * Provides adding of Flash Messages
	 * 
	 * @param msg
	 * @param type
	 */
	public void addFlashMessage(String msg, String type) {
		jsonClient.getFlashMessages().add(new FlashMessage(msg, type));
	}
}
