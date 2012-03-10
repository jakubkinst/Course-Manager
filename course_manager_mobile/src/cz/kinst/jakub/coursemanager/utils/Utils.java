package cz.kinst.jakub.coursemanager.utils;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;

public class Utils {

	public static Date getDateFromDBString(String sDate){
		try {
			return new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").parse(sDate);
		} catch (ParseException e) {
			e.printStackTrace();
			return null;
		}  
	}
	
}
