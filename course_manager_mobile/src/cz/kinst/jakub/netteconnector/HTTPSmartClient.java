/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package cz.kinst.jakub.netteconnector;

import java.io.BufferedInputStream;
import java.io.BufferedReader;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.Reader;
import java.io.Serializable;
import java.io.StringWriter;
import java.io.Writer;
import java.net.URLDecoder;
import java.util.ArrayList;
import java.util.HashMap;

import org.apache.http.Header;
import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.util.ByteArrayBuffer;

import android.util.Log;
import cz.kinst.jakub.coursemanager.CourseManagerConnector;

public class HTTPSmartClient implements Serializable {

	/**
	 * 
	 */
	private static final long serialVersionUID = -1500477031448176559L;
	HashMap<String, String> cookies = new HashMap<String, String>();

	public HashMap<String, String> getCookies() {
		return cookies;
	}

	public String getJSON(String url, ArrayList<NameValuePair> getArgs,
			ArrayList<NameValuePair> postArgs) {
		String result;
		try {
			result = convertStreamToString(getInputStream(url, getArgs,
					postArgs));
		} catch (Exception e) {
			Log.e(CourseManagerConnector.LOGTAG,
					"Error in http connection" + e.toString());
			result = "";
		}
		Log.d(CourseManagerConnector.LOGTAG, "Response: " + result);
		return result;
	}

	public InputStream getInputStream(String url,
			ArrayList<NameValuePair> getArgs, ArrayList<NameValuePair> postArgs)
			throws IllegalStateException, IOException {

		String result;
		InputStream is;

		if (getArgs == null) {
			getArgs = new ArrayList<NameValuePair>();
		}
		if (postArgs == null) {
			postArgs = new ArrayList<NameValuePair>();
		}

		HttpClient httpclient = new DefaultHttpClient();
		url = packGetParams(url, getArgs);
		Log.d("coursemanager", "Request: " + url);
		HttpPost httppost = new HttpPost(url);
		if (cookies != null && !cookies.isEmpty()) {
			String cks = "";
			for (HashMap.Entry<String, String> entry : cookies.entrySet()) {
				String name = entry.getKey();
				String value = entry.getValue();
				cks = cks + ";" + name + "=" + value;
				Log.d(CourseManagerConnector.LOGTAG, "Loading cookie " + name
						+ ":" + value);
			}
			cks = cks.substring(1);
			httppost.setHeader("cookie", cks);
		}
		httppost.setEntity(new UrlEncodedFormEntity(postArgs));
		HttpResponse response = httpclient.execute(httppost);

		saveCookies(response.getHeaders("Set-Cookie"));

		HttpEntity entity = response.getEntity();
		is = entity.getContent();
		return is;
	}

	public File downloadFile(String url, ArrayList<NameValuePair> getArgs,
			ArrayList<NameValuePair> postArgs, String saveTo) {
		File f = new File(saveTo);
		try {
			InputStream is = getInputStream(url, getArgs, postArgs);
			BufferedInputStream bis = new BufferedInputStream(is);

			/*
			 * Read bytes to the Buffer until there is nothing more to read(-1).
			 */
			ByteArrayBuffer baf = new ByteArrayBuffer(50);
			int current = 0;
			while ((current = bis.read()) != -1) {
				baf.append((byte) current);
			}

			/* Convert the Bytes read to a String. */
			FileOutputStream fos = new FileOutputStream(f);
			fos.write(baf.toByteArray());
			fos.close();
		} catch (IOException e) {
		}
		return f;
	}

	private void saveCookies(Header[] headers) {
		for (Header h : headers) {
			String header = URLDecoder.decode(h.getValue());
			String[] cks = header.split(";");
			String c = cks[0];
			for (String subC : c.split(",")) {
				String name = subC.split("=")[0].trim();
				String value = subC.split("=")[1].trim();
				cookies.put(name, value);
				Log.d(CourseManagerConnector.LOGTAG, "Saving cookie " + name
						+ ":" + value);
			}
		}
	}

	private String packGetParams(String url, ArrayList<NameValuePair> getArgs) {
		url = url + "?";
		for (NameValuePair par : getArgs) {
			url = url + par.getName() + '=' + par.getValue() + "&";
		}
		return url;
	}

	public String convertStreamToString(InputStream is) throws IOException {
		/*
		 * To convert the InputStream to String we use the Reader.read(char[]
		 * buffer) method. We iterate until the Reader return -1 which means
		 * there's no more data to read. We use the StringWriter class to
		 * produce the string.
		 */
		if (is != null) {
			Writer writer = new StringWriter();

			char[] buffer = new char[1024];
			try {
				Reader reader = new BufferedReader(new InputStreamReader(is,
						"UTF-8"));
				int n;
				while ((n = reader.read(buffer)) != -1) {
					writer.write(buffer, 0, n);
				}
			} finally {
				is.close();
			}
			return writer.toString();
		} else {
			return "";
		}
	}
}
