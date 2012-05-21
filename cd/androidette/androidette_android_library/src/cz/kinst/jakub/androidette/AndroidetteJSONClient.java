package cz.kinst.jakub.androidette;

import java.io.Serializable;
import java.util.ArrayList;

import org.json.JSONArray;
import org.json.JSONObject;

import android.util.Log;

/**
 * Utility class responsible for converting JSON from string to apache
 * JSONObject and for handling special parts of JSON data. Such as Nette Flash
 * Messages or Error/Dump messages.
 * 
 * User can send special messages from web application for debugging purposes.
 * If user posts String in ERROR or DUMP field of JSON array, the String will be
 * automatically printed out to the Android LogCat.
 * 
 * @author Jakub Kinst
 */
public class AndroidetteJSONClient implements Serializable {

	/**
	 * Special Error message
	 */
	private static final String ERROR = "__error";

	/**
	 * Special Dump message
	 */
	private static final String DUMP = "__dump";

	private static final String LOG_TAG = "Androidette_NetteJSONClient";

	/**
	 * Nette String
	 */
	private static final String TYPE_STRING = "type";

	/**
	 * Nette String
	 */
	private static final String MESSAGE_STRING = "message";

	/**
	 * Nette String
	 */
	private static final String FLASHES_STRING = "flashes";

	/**
	 * UID for serialization
	 */
	private static final long serialVersionUID = -6592494527406348077L;

	/**
	 * Array of Nette Flash Messages
	 */
	ArrayList<FlashMessage> flashmessages = new ArrayList<FlashMessage>();

	/**
	 * Main processing function handling JSON data
	 * 
	 * @param stringData
	 * @return
	 */
	public JSONObject processStringToJSON(String stringData) {
		try {
			JSONObject jsonData = new JSONObject(stringData);

			// uncomment to log each json root array item
			// for (@SuppressWarnings("unchecked")
			// Iterator<String> it = jsonData.keys(); it.hasNext();) {
			// String key = it.next();
			// Log.d(LOG_TAG, key + "=" + jsonData.get(key).toString());
			// }

			// Special messages:
			// when sent to template in Nette Application
			// they will be automatically printed to LogCat
			if (jsonData.has(DUMP)) {
				Log.d("coursemanager", "JSON DUMP: " + jsonData.getString(DUMP));
			}
			if (jsonData.has(ERROR)) {
				Log.e("coursemanager",
						"JSON ERROR: " + jsonData.getString(ERROR));
			}

			// fill flash messages
			flashmessages.clear();
			if (jsonData.has(FLASHES_STRING)) {
				JSONArray flashes = jsonData.getJSONArray(FLASHES_STRING);
				for (int i = 0; i < flashes.length(); i++) {
					JSONObject flash = flashes.getJSONObject(i);
					String msg = flash.getString(MESSAGE_STRING);
					String type = flash.getString(TYPE_STRING);
					flashmessages.add(new FlashMessage(msg, type));
				}
			}

			// cleanup
			jsonData.remove(DUMP);
			jsonData.remove(ERROR);
			jsonData.remove(FLASHES_STRING);
			return jsonData;

		} catch (Exception e) {
			Log.e(LOG_TAG, e.getMessage());
			return new JSONObject();
		}
	}

	/**
	 * Getter for flash messages
	 * 
	 * @return
	 */
	public ArrayList<FlashMessage> getFlashMessages() {
		return flashmessages;
	}
}
