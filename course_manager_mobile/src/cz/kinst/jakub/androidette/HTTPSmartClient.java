package cz.kinst.jakub.androidette;

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
import java.util.HashMap;
import java.util.List;
import java.util.Map.Entry;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.client.protocol.ClientContext;
import org.apache.http.cookie.Cookie;
import org.apache.http.entity.mime.MultipartEntity;
import org.apache.http.entity.mime.content.FileBody;
import org.apache.http.entity.mime.content.StringBody;
import org.apache.http.impl.client.BasicCookieStore;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.protocol.BasicHttpContext;
import org.apache.http.protocol.HttpContext;
import org.apache.http.util.ByteArrayBuffer;

import android.util.Log;

public class HTTPSmartClient implements Serializable {

	/**
	 * UID for serialization
	 */
	private static final long serialVersionUID = -1500477031448176559L;
	private static final String LOG_TAG = "Androidette_HTTPSmartClient";
	private boolean debugMode = false;

	/**
	 * List of saved cookies Theese are collected from server responses and
	 * packed in following requests
	 */
	private List<SerializableCookie> cookies = new ArrayList<SerializableCookie>();

	public String convertStreamToString(InputStream is) throws IOException {

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

	public File downloadFile(String url, ArrayList<NameValuePair> getArgs,
			ArrayList<NameValuePair> postArgs, String pathToSave) {
		File file = new File(pathToSave);
		try {
			InputStream inputStream = getInputStream(url, getArgs, postArgs,
					null);
			BufferedInputStream bufferedInputStream = new BufferedInputStream(
					inputStream);

			/*
			 * Read bytes to the Buffer until there is nothing more to read(-1).
			 */
			ByteArrayBuffer baf = new ByteArrayBuffer(50);
			int current = 0;
			while ((current = bufferedInputStream.read()) != -1) {
				baf.append((byte) current);
			}

			/* Convert the Bytes read to a String. */
			FileOutputStream fileOutputStream = new FileOutputStream(file);
			fileOutputStream.write(baf.toByteArray());
			fileOutputStream.close();
		} catch (IOException e) {
			e.printStackTrace();
		}
		return file;
	}

	public List<SerializableCookie> getCookies() {
		return cookies;
	}

	public InputStream getInputStream(String url,
			ArrayList<NameValuePair> getArgs,
			ArrayList<NameValuePair> postArgs, HashMap<String, File> fileArgs)
			throws IllegalStateException, IOException {

		InputStream inputStream;

		if (getArgs == null) {
			getArgs = new ArrayList<NameValuePair>();
		}
		if (postArgs == null) {
			postArgs = new ArrayList<NameValuePair>();
		}
		if (fileArgs == null) {
			fileArgs = new HashMap<String, File>();
		}

		// Creating a local HTTP context
		HttpContext localContext = new BasicHttpContext();

		// Create cookie store and fill it with already saved cookies
		BasicCookieStore cookieStore = new BasicCookieStore();
		for (Cookie cookie : cookies) {
			cookieStore.addCookie(cookie);
		}

		// Bind custom cookie store to the local context
		localContext.setAttribute(ClientContext.COOKIE_STORE, cookieStore);

		HttpClient httpclient = new DefaultHttpClient();

		// add GET parameters to URL
		url = packGetParams(url, getArgs);

		if (debugMode) {
			Log.d(LOG_TAG, "Request: " + url);
		}

		HttpPost httppost = new HttpPost(url);

		// MultipartEntity is an Entity which supports both string params
		// and File params
		MultipartEntity mpEntity = new MultipartEntity();

		// fill with POST params
		for (NameValuePair nameValuePair : postArgs) {
			mpEntity.addPart(nameValuePair.getName(), new StringBody(
					nameValuePair.getValue()));
		}

		// fill with file params
		for (Entry<String, File> file : fileArgs.entrySet()) {
			mpEntity.addPart(file.getKey(), new FileBody(file.getValue()));
		}

		httppost.setEntity(mpEntity);

		// execute http request and retrieve response
		HttpResponse response = httpclient.execute(httppost, localContext);

		// save retrieved cookies
		for (Cookie cookie : cookieStore.getCookies()) {
			this.cookies.add(new SerializableCookie(cookie));
		}

		// get body from response
		HttpEntity responseEntity = response.getEntity();
		if (responseEntity != null) {
			inputStream = responseEntity.getContent();
			return inputStream;
		} else {
			return null;
		}
	}

	public String getJSON(String url, ArrayList<NameValuePair> getArgs,
			ArrayList<NameValuePair> postArgs) {
		return getJSON(url, getArgs, postArgs, null);
	}

	public String getJSON(String url, ArrayList<NameValuePair> getArgs,
			ArrayList<NameValuePair> postArgs, HashMap<String, File> files) {
		String result;
		try {
			result = convertStreamToString(getInputStream(url, getArgs,
					postArgs, files));
		} catch (Exception e) {
			Log.e(LOG_TAG, "Error in http connection: " + e.toString());
			result = "";
		}
		if (debugMode) {
			Log.d(LOG_TAG, "Response: " + result);
		}
		return result;
	}

	private String packGetParams(String url, ArrayList<NameValuePair> getArgs) {
		if (!getArgs.isEmpty()) {
			url = url.concat("?");
			String prefix = "";
			for (NameValuePair arg : getArgs) {

				url = url.concat(prefix);
				prefix = "&";
				url = url.concat(arg.getName() + '=' + arg.getValue());
			}
		}
		return url;
	}

	public void setCookies(List<SerializableCookie> cookies) {
		this.cookies = cookies;
	}
}
