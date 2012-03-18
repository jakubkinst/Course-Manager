package cz.kinst.jakub.coursemanager.utils;

import java.math.BigDecimal;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

public class Utils {

	public static Date getDateFromDBString(String sDate) {
		try {
			return new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").parse(sDate);
		} catch (ParseException e) {
			e.printStackTrace();
			return null;
		}
	}

	public static ArrayList<JSONObject> getJSONObjectArray(JSONArray jsonArray)
			throws JSONException {
		ArrayList<JSONObject> array = new ArrayList<JSONObject>();
		for (int i = 0; i < jsonArray.length(); i++) {
			array.add(jsonArray.getJSONObject(i));
		}
		return array;
	}

	public static double round(double unrounded, int precision) {
		BigDecimal bd = new BigDecimal(unrounded);
		BigDecimal rounded = bd.setScale(precision, BigDecimal.ROUND_HALF_UP);
		return rounded.doubleValue();
	}

	public static String[] maskArray(String[] array, boolean[] mask) {
		ArrayList<String> list = new ArrayList<String>();
		for (int i = 0; i < mask.length; i++) {
			if (mask[i])
				list.add(array[i]);
		}
		return list.toArray(new String[list.size()]);
	}

	public static String implode(String[] array, String delimiter) {
		String AsImplodedString;
		if (array.length == 0) {
			AsImplodedString = "";
		} else {
			StringBuffer sb = new StringBuffer();
			sb.append(array[0]);
			for (int i = 1; i < array.length; i++) {
				sb.append(delimiter);
				sb.append(array[i]);
			}
			AsImplodedString = sb.toString();
		}
		return AsImplodedString;
	}

	public static String[] getTruePositions(boolean[] selected) {
		ArrayList<String> list = new ArrayList<String>();
		int i = 0;
		for (boolean s : selected) {
			if (s)
				list.add(String.valueOf(i));
			i++;
		}
		return list.toArray(new String[list.size()]);
	}

}
