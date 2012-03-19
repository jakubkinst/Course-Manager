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
import java.net.Socket;
import java.net.UnknownHostException;
import java.security.KeyManagementException;
import java.security.KeyStore;
import java.security.KeyStoreException;
import java.security.NoSuchAlgorithmException;
import java.security.UnrecoverableKeyException;
import java.security.cert.CertificateException;
import java.security.cert.X509Certificate;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map.Entry;

import javax.net.ssl.SSLContext;
import javax.net.ssl.TrustManager;
import javax.net.ssl.X509TrustManager;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.HttpVersion;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.client.protocol.ClientContext;
import org.apache.http.conn.ClientConnectionManager;
import org.apache.http.conn.scheme.PlainSocketFactory;
import org.apache.http.conn.scheme.Scheme;
import org.apache.http.conn.scheme.SchemeRegistry;
import org.apache.http.conn.ssl.SSLSocketFactory;
import org.apache.http.cookie.Cookie;
import org.apache.http.entity.mime.MultipartEntity;
import org.apache.http.entity.mime.content.FileBody;
import org.apache.http.entity.mime.content.StringBody;
import org.apache.http.impl.client.BasicCookieStore;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.impl.conn.tsccm.ThreadSafeClientConnManager;
import org.apache.http.params.BasicHttpParams;
import org.apache.http.params.HttpParams;
import org.apache.http.params.HttpProtocolParams;
import org.apache.http.protocol.BasicHttpContext;
import org.apache.http.protocol.HTTP;
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

		HttpClient httpclient = getNewHttpSecureClient();

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

	public class MySSLSocketFactory extends SSLSocketFactory {
		SSLContext sslContext = SSLContext.getInstance("TLS");

		public MySSLSocketFactory(KeyStore truststore)
				throws NoSuchAlgorithmException, KeyManagementException,
				KeyStoreException, UnrecoverableKeyException {
			super(truststore);

			TrustManager tm = new X509TrustManager() {
				public void checkClientTrusted(X509Certificate[] chain,
						String authType) throws CertificateException {
				}

				public void checkServerTrusted(X509Certificate[] chain,
						String authType) throws CertificateException {
				}

				public X509Certificate[] getAcceptedIssuers() {
					return null;
				}
			};

			sslContext.init(null, new TrustManager[] { tm }, null);
		}

		@Override
		public Socket createSocket(Socket socket, String host, int port,
				boolean autoClose) throws IOException, UnknownHostException {
			return sslContext.getSocketFactory().createSocket(socket, host,
					port, autoClose);
		}

		@Override
		public Socket createSocket() throws IOException {
			return sslContext.getSocketFactory().createSocket();
		}
	}

	public HttpClient getNewHttpSecureClient() {
		try {
			KeyStore trustStore = KeyStore.getInstance(KeyStore
					.getDefaultType());
			trustStore.load(null, null);

			SSLSocketFactory sf = new MySSLSocketFactory(trustStore);
			sf.setHostnameVerifier(SSLSocketFactory.ALLOW_ALL_HOSTNAME_VERIFIER);

			HttpParams params = new BasicHttpParams();
			HttpProtocolParams.setVersion(params, HttpVersion.HTTP_1_1);
			HttpProtocolParams.setContentCharset(params, HTTP.UTF_8);

			SchemeRegistry registry = new SchemeRegistry();
			registry.register(new Scheme("http", PlainSocketFactory
					.getSocketFactory(), 80));
			registry.register(new Scheme("https", sf, 443));

			ClientConnectionManager ccm = new ThreadSafeClientConnManager(
					params, registry);

			return new DefaultHttpClient(ccm, params);
		} catch (Exception e) {
			return new DefaultHttpClient();
		}
	}
}
