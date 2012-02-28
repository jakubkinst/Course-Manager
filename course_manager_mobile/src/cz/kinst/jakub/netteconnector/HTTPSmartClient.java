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
import java.util.ArrayList;
import java.util.List;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.client.protocol.ClientContext;
import org.apache.http.cookie.Cookie;
import org.apache.http.impl.client.BasicCookieStore;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.protocol.BasicHttpContext;
import org.apache.http.protocol.HttpContext;
import org.apache.http.util.ByteArrayBuffer;

import android.util.Log;
import cz.kinst.jakub.coursemanager.CourseManagerConnector;

public class HTTPSmartClient implements Serializable {

	/**
	 * 
	 */
	private static final String TAG = "HTTPSmartClient";
	private static final long serialVersionUID = -1500477031448176559L;
	private List<SerializableCookie> cookies = new ArrayList<SerializableCookie>();

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

		InputStream is;

		if (getArgs == null) {
			getArgs = new ArrayList<NameValuePair>();
		}
		if (postArgs == null) {
			postArgs = new ArrayList<NameValuePair>();
		}

		// Creating a local HTTP context
		HttpContext localContext = new BasicHttpContext();

		BasicCookieStore cookieStore = new BasicCookieStore();
		for (Cookie cookie : cookies) {
			cookieStore.addCookie(cookie);
		}
		// Bind custom cookie store to the local context
		localContext.setAttribute(ClientContext.COOKIE_STORE, cookieStore);

		HttpClient httpclient = new DefaultHttpClient();
		url = packGetParams(url, getArgs);
		Log.d(TAG, "Request: " + url);
		HttpPost httppost = new HttpPost(url);

		httppost.setEntity(new UrlEncodedFormEntity(postArgs));
		HttpResponse response = httpclient.execute(httppost, localContext);

		for (Cookie cookie : cookieStore.getCookies()) {
			this.cookies.add(new SerializableCookie(cookie));
		}
		HttpEntity entity = response.getEntity();
		if (entity != null) {
			is = entity.getContent();
			return is;
		} else
			return null;
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
