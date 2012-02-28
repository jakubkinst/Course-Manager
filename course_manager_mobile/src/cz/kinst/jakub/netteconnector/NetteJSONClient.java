/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package cz.kinst.jakub.netteconnector;

import android.net.ParseException;
import android.util.Log;
import cz.kinst.jakub.netteconnector.FlashMessage;

import java.io.Serializable;
import java.util.ArrayList;
import java.util.Iterator;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

/**
 *
 * @author JerRy
 */
public class NetteJSONClient implements Serializable {

	/**
	 * 
	 */
	private static final long serialVersionUID = -6592494527406348077L;
	ArrayList<FlashMessage> flashmessages = new ArrayList<FlashMessage>();

	public JSONObject getJSONObject(String res) {
		try {
			JSONObject json_data = new JSONObject(res);
			for (Iterator<String> it = json_data.keys(); it.hasNext();) {
				String key = it.next();
				Log.d("coursemanager_json", key+"="+json_data.get(key).toString());
			}

			if (json_data.has("__dump")) {
				Log.d("coursemanager", "JSON DUMP: " + json_data.getString("__dump"));
			}
			if (json_data.has("__error")) {
				Log.e("coursemanager", "JSON ERROR: " + json_data.getString("__error"));
			}
			flashmessages.clear();
			if (json_data.has("flashes")) {
				JSONArray flashes = json_data.getJSONArray("flashes");
				for (int i = 0; i < flashes.length(); i++) {
					JSONObject flash = flashes.getJSONObject(i);
					String msg = flash.getString("message");
					String type = flash.getString("type");
					flashmessages.add(new FlashMessage(msg, type));
				}
			}
			json_data.remove("__dump");
			json_data.remove("__error");
			json_data.remove("flashes");
			return json_data;

		} catch (JSONException e1) {
			Log.e("coursemanager", e1.getMessage());
		} catch (ParseException e1) {
			e1.printStackTrace();
		}
		return new JSONObject();
	}

	public ArrayList<FlashMessage> getFlashMessages() {
		return flashmessages;
	}
}
