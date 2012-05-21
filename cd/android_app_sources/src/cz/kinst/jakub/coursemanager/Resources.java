package cz.kinst.jakub.coursemanager;

import java.util.ArrayList;
import java.util.List;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import android.content.Context;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ListView;
import android.widget.TextView;
import cz.kinst.jakub.coursemanager.utils.DownloadTask;
import cz.kinst.jakub.coursemanager.utils.Utils;

/**
 * Activity showing course-related resources user can download file to device by
 * clicking on it in the list
 * 
 * @author Jakub Kinst
 * 
 */
public class Resources extends CMActivity {

	/**
	 * UID for serialization
	 */
	private static final long serialVersionUID = 5108528581652454927L;

	/**
	 * Course ID
	 */
	private int cid;

	// MENU
	public int MENU_NEW_TOPIC;

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		this.cid = getIntent().getExtras().getInt("cid");
		setContentView(R.layout.resources);
		reload();
	}

	@Override
	protected JSONObject reloadWork() throws JSONException {
		JSONObject resources = new JSONObject();
		ArrayList<NameValuePair> args = new ArrayList<NameValuePair>();
		args.add(new BasicNameValuePair("cid", String.valueOf(this.cid)));
		args.add(new BasicNameValuePair("pages-page", String.valueOf(this.page)));
		resources = courseManagerCon.getAction("resource", "homepage", args,
				new ArrayList<NameValuePair>());
		return resources;
	}

	@Override
	public void gotData(JSONObject data) throws JSONException {
		JSONObject course = data.getJSONObject("activeCourse");
		setTitle(course.getString("name") + " > " + getText(R.string.resources));

		((ListView) (findViewById(R.id.resources)))
				.setAdapter(new ResourcesAdapter(
						this,
						R.layout.resource_row,
						Utils.getJSONObjectArray(data.getJSONArray("resources"))));
	}

	/**
	 * ArrayAdapter for Resources ListView
	 * 
	 * @author Jakub Kinst
	 * 
	 */
	public class ResourcesAdapter extends ArrayAdapter<JSONObject> {

		public ResourcesAdapter(Context context, int textViewResourceId,
				List<JSONObject> objects) {
			super(context, textViewResourceId, objects);
		}

		@Override
		public View getView(int position, View convertView, ViewGroup parent) {
			View v = convertView;
			if (v == null) {
				LayoutInflater vi = (LayoutInflater) getSystemService(Context.LAYOUT_INFLATER_SERVICE);
				v = vi.inflate(R.layout.resource_row, null);
			}
			final JSONObject resource = getItem(position);
			try {
				((TextView) (v.findViewById(R.id.filename))).setText(resource
						.getString("name"));
				int size = resource.getInt("size") / 1024;
				((TextView) (v.findViewById(R.id.size))).setText(size + " KB");

			} catch (JSONException e) {
				e.printStackTrace();
			}
			v.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					new DownloadTask(Resources.this, courseManagerCon)
							.execute(resource);
				}
			});
			return v;
		}
	}

}
