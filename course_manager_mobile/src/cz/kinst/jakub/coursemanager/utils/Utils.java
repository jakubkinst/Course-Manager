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

	public static Date getDateFromDBString(String sDate){
		try {
			return new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").parse(sDate);
		} catch (ParseException e) {
			e.printStackTrace();
			return null;
		}  
	}
	
	public static ArrayList<JSONObject> getJSONObjectArray(JSONArray jsonArray) throws JSONException {
		ArrayList<JSONObject> array = new ArrayList<JSONObject>();
		for (int i = 0; i < jsonArray.length(); i++) {
			array.add(jsonArray.getJSONObject(i));
		}
		return array;
	}
	
	public static double round(double unrounded, int precision)
	{
	    BigDecimal bd = new BigDecimal(unrounded);
	    BigDecimal rounded = bd.setScale(precision, BigDecimal.ROUND_HALF_UP);
	    return rounded.doubleValue();
	}
	
}
