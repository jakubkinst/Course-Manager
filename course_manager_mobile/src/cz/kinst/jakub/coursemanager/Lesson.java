package cz.kinst.jakub.coursemanager;

import java.io.File;
import java.util.ArrayList;
import java.util.List;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Environment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

public class Lesson extends CMActivity {

	private int lid;
	public ArrayList<Integer> pages;
	public int page = 1;

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.lesson);
		this.lid = getIntent().getExtras().getInt("lid");
		((TextView) (findViewById(R.id.category1).findViewById(R.id.name)))
				.setText(R.string.resources);
		((TextView) (findViewById(R.id.category2).findViewById(R.id.name)))
				.setText(R.string.comments);
		reload();
	}

	@Override
	protected JSONObject reloadWork() throws JSONException {
		JSONObject lesson = new JSONObject();
		ArrayList<NameValuePair> args = new ArrayList<NameValuePair>();
		args.add(new BasicNameValuePair("lid", String.valueOf(this.lid)));
		args.add(new BasicNameValuePair("pages-page", String.valueOf(this.page)));
		lesson = cm.getAction("lesson", "homepage", args,
				new ArrayList<NameValuePair>());
		return lesson;
	}

	@Override
	public void gotData(JSONObject data) throws JSONException {

		Button prev = (Button) findViewById(R.id.previous_page);
		Button next = (Button) findViewById(R.id.nextt_page);
		TextView pageLabel = (TextView) findViewById(R.id.page);
		if (data.getJSONObject("pages").has("steps")) {
			JSONArray steps = data.getJSONObject("pages").getJSONArray("steps");
			pages = new ArrayList<Integer>();
			for (int i = 0; i < steps.length(); i++) {
				pages.add(new Integer(steps.getInt(i)));
			}

			pageLabel.setText(String.valueOf(page));
			if (page < 2)
				prev.setVisibility(View.INVISIBLE);
			else
				prev.setVisibility(View.VISIBLE);
			if (page >= pages.size())
				next.setVisibility(View.INVISIBLE);
			else
				next.setVisibility(View.VISIBLE);

			pageLabel.setVisibility(View.VISIBLE);

			prev.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					page = page - 1;
					reload();
				}
			});
			next.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					page = page + 1;
					reload();
				}
			});
		} else {
			prev.setVisibility(View.GONE);
			next.setVisibility(View.GONE);
			pageLabel.setVisibility(View.GONE);
		}

		JSONObject lesson = data.getJSONObject("lesson");
		((TextView) (findViewById(R.id.topic))).setText(lesson
				.getString("topic"));
		((TextView) (findViewById(R.id.date)))
				.setText(lesson.getString("date"));
		ArrayList<JSONObject> resources = new ArrayList<JSONObject>();
		JSONArray resourcesJSON = data.getJSONArray("resources");
		for (int i = 0; i < resourcesJSON.length(); i++) {
			resources.add(resourcesJSON.getJSONObject(i));
		}
		ArrayList<JSONObject> comments = new ArrayList<JSONObject>();
		JSONArray commentsJSON = data.getJSONArray("comments");
		for (int i = 0; i < commentsJSON.length(); i++) {
			comments.add(commentsJSON.getJSONObject(i));
		}
		// ListView list = new ListView(this);
		((ListView) (findViewById(R.id.resources)))
				.setAdapter(new ResourceAdapter(this,
						android.R.layout.simple_list_item_1, resources));
		((ListView) (findViewById(R.id.comments)))
				.setAdapter(new CommentsAdapter(this,
						android.R.layout.simple_list_item_1, comments));
	}

	public class ResourceAdapter extends ArrayAdapter<JSONObject> {

		public List<JSONObject> resources;

		public ResourceAdapter(Context context, int textViewResourceId,
				List<JSONObject> objects) {
			super(context, textViewResourceId, objects);
			this.resources = objects;
		}

		@Override
		public View getView(int position, View convertView, ViewGroup parent) {
			View v = convertView;
			if (v == null) {
				LayoutInflater vi = (LayoutInflater) getSystemService(Context.LAYOUT_INFLATER_SERVICE);
				v = vi.inflate(R.layout.resource_row, null);
			}
			final JSONObject resource = resources.get(position);
			try {
				((TextView) (v.findViewById(R.id.filename))).setText(resource
						.getString("name"));

			} catch (JSONException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}

			v.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					new DownloadTask().execute(resource);
				}
			});

			return v;
		}
	}
	
	
	
	class DownloadTask extends AsyncTask<JSONObject,Void, File> {

		protected void onPreExecute() {
			setProgressBarIndeterminateVisibility(true);
			Toast.makeText(Lesson.this,
					"Download started", 2000)
					.show();
		}

		protected File doInBackground(JSONObject... resource) {
			File myFolder = new File(Environment
					.getExternalStorageDirectory()
					+ "/CourseManager_downloads");
			myFolder.mkdirs();
			File file = new File("");
			try {
				file = cm.getResource(resource[0].getInt("id"),
						myFolder + "/" + resource[0].getString("name"));
			} catch (JSONException e) {
			}			
			return file;
		}

		protected void onPostExecute(File file) {
			Toast.makeText(Lesson.this,
					"File saved to " + file.getAbsolutePath(), 2000)
					.show();
			try {
				Intent intent = new Intent();
				intent.setAction(android.content.Intent.ACTION_VIEW);
				intent.setDataAndType(Uri.fromFile(file),
						getMIMEType(file));
				startActivity(intent);
			} catch (Exception e) {
				Toast.makeText(Lesson.this,
						"No application found to open this file. File was just saved.", 2000)
						.show();
			}
			setProgressBarIndeterminateVisibility(false);

		}


	}

	public String getMIMEType(File f) {
		String filenameArray[] = f.getName().split("\\.");
		String e = filenameArray[filenameArray.length - 1];
		String mime = "";
		e = e.toLowerCase();

		if (e.equals("jpg"))
			mime = "image/jpg";
		if (e.equals("jpeg"))
			mime = "image/jpeg";
		if (e.equals("png"))
			mime = "image/png";
		if (e.equals("gif"))
			mime = "image/gif";
		if (e.equals("mp3"))
			mime = "audio/mp3";
		if (e.equals("html"))
			mime = "text/html";
		if (e.equals("pdf"))
			mime = "application/pdf";
		if (e.equals("doc"))
			mime = "application/doc";
		if (e.equals("apk"))
			mime = "application/vnd.android.package-archive";
		if (e.equals("txt"))
			mime = "text/plain";

		return mime;
	}

	public class CommentsAdapter extends ArrayAdapter<JSONObject> {

		public List<JSONObject> comments;

		public CommentsAdapter(Context context, int textViewResourceId,
				List<JSONObject> objects) {
			super(context, textViewResourceId, objects);
			this.comments = objects;
		}

		@Override
		public View getView(int position, View convertView, ViewGroup parent) {
			View v = convertView;
			if (v == null) {
				LayoutInflater vi = (LayoutInflater) getSystemService(Context.LAYOUT_INFLATER_SERVICE);
				v = vi.inflate(R.layout.comment_row, null);
			}
			JSONObject comment = comments.get(position);
			try {

				((TextView) (v.findViewById(R.id.content))).setText(comment
						.getString("content"));
				((TextView) (v.findViewById(R.id.added))).setText(comment
						.getString("added"));
				((TextView) (v.findViewById(R.id.author))).setText(comment
						.getJSONObject("user").getString("firstname")
						+ " "
						+ comment.getJSONObject("user").getString("lastname"));
			} catch (JSONException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			return v;
		}
	}
}
