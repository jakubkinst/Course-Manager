package cz.kinst.jakub.coursemanager.utils;

import java.io.File;
import java.math.BigDecimal;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.database.Cursor;
import android.net.Uri;
import android.provider.MediaStore;

/**
 * Utility class provides helper methods
 * 
 * @author Jakub Kinst
 * 
 */
public class Utils {

	/**
	 * Converts default PHP DateTime format to Java Date object
	 * 
	 * @param sDate
	 *            date string
	 * @return
	 */
	public static Date getDateFromDBString(String sDate) {
		try {
			return new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").parse(sDate);
		} catch (ParseException e) {
			e.printStackTrace();
			return null;
		}
	}

	/**
	 * Converts JSONArray to arraylist of JSONObjects
	 * 
	 * @param jsonArray
	 * @return
	 * @throws JSONException
	 */
	public static ArrayList<JSONObject> getJSONObjectArray(JSONArray jsonArray)
			throws JSONException {
		ArrayList<JSONObject> array = new ArrayList<JSONObject>();
		for (int i = 0; i < jsonArray.length(); i++) {
			array.add(jsonArray.getJSONObject(i));
		}
		return array;
	}

	/**
	 * Mathematical round function with precision
	 * 
	 * @param unrounded
	 * @param precision
	 * @return
	 */
	public static double round(double unrounded, int precision) {
		BigDecimal bd = new BigDecimal(unrounded);
		BigDecimal rounded = bd.setScale(precision, BigDecimal.ROUND_HALF_UP);
		return rounded.doubleValue();
	}

	/**
	 * Filters input string array based on input boolean array
	 * 
	 * @param array
	 * @param mask
	 * @return
	 */
	public static String[] maskArray(String[] array, boolean[] mask) {
		ArrayList<String> list = new ArrayList<String>();
		for (int i = 0; i < mask.length; i++) {
			if (mask[i]) {
				list.add(array[i]);
			}
		}
		return list.toArray(new String[list.size()]);
	}

	/**
	 * Emulates standard PHP implode() function
	 * 
	 * @param array
	 * @param delimiter
	 * @return
	 */
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

	/**
	 * Returns array of positions of true boolean values in array
	 * 
	 * @param selected
	 * @return
	 */
	public static String[] getTruePositions(boolean[] selected) {
		ArrayList<String> list = new ArrayList<String>();
		int i = 0;
		for (boolean s : selected) {
			if (s) {
				list.add(String.valueOf(i));
			}
			i++;
		}
		return list.toArray(new String[list.size()]);
	}

	/**
	 * Returns real File path from Android Resource URI
	 * 
	 * @param contentUri
	 * @param context
	 * @return
	 */
	public static String getRealPathFromURI(Uri contentUri, Activity context) {
		String[] proj = { MediaStore.Files.FileColumns.DATA };
		Cursor cursor = context.managedQuery(contentUri, proj, // Which columns
																// to return
				null, // WHERE clause; which rows to return (all rows)
				null, // WHERE clause selection arguments (none)
				null); // Order-by clause (ascending by name)
		int column_index = cursor
				.getColumnIndexOrThrow(MediaStore.Images.Media.DATA);
		cursor.moveToFirst();
		return cursor.getString(column_index);
	}

	/**
	 * Resolves MIME type of a file f based on file extension
	 * 
	 * @param f
	 * @return
	 */
	public static String getMIMEType(File f) {
		String filenameArray[] = f.getName().split("\\.");
		String e = filenameArray[filenameArray.length - 1];
		String mime = "";
		e = e.toLowerCase();

		if (e.equals("jpg")) {
			mime = "image/jpg";
		}
		if (e.equals("jpeg")) {
			mime = "image/jpeg";
		}
		if (e.equals("png")) {
			mime = "image/png";
		}
		if (e.equals("gif")) {
			mime = "image/gif";
		}
		if (e.equals("mp3")) {
			mime = "audio/mp3";
		}
		if (e.equals("html")) {
			mime = "text/html";
		}
		if (e.equals("pdf")) {
			mime = "application/pdf";
		}
		if (e.equals("doc")) {
			mime = "application/doc";
		}
		if (e.equals("apk")) {
			mime = "application/vnd.android.package-archive";
		}
		if (e.equals("txt")) {
			mime = "text/plain";
		}

		return mime;
	}
}
