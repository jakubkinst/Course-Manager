package cz.kinst.jakub.netteconnector;

import java.io.Serializable;
import java.util.ArrayList;

import org.json.JSONArray;
import org.json.JSONObject;

import android.util.Log;

/**
 * 
 * @author Jakub Kinst
 */
public class AndroidetteJSONClient implements Serializable {

	private static final String LOG_TAG = "Androidette_NetteJSONClient";

	private static final String TYPE_STRING = "type";

	private static final String MESSAGE_STRING = "message";

	private static final String FLASHES_STRING = "flashes";

	/**
	 * UID for serialization
	 */
	private static final long serialVersionUID = -6592494527406348077L;

	ArrayList<FlashMessage> flashmessages = new ArrayList<FlashMessage>();

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
			if (jsonData.has("__dump")) {
				Log.d("coursemanager",
						"JSON DUMP: " + jsonData.getString("__dump"));
			}
			if (jsonData.has("__error")) {
				Log.e("coursemanager",
						"JSON ERROR: " + jsonData.getString("__error"));
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
			jsonData.remove("__dump");
			jsonData.remove("__error");
			jsonData.remove(FLASHES_STRING);
			return jsonData;

		} catch (Exception e) {
			Log.e(LOG_TAG, e.getMessage());
			return new JSONObject();
		}
	}

	public ArrayList<FlashMessage> getFlashMessages() {
		return flashmessages;
	}
}
