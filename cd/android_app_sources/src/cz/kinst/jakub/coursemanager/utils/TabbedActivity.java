package cz.kinst.jakub.coursemanager.utils;

import java.util.ArrayList;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup.LayoutParams;
import android.widget.Button;
import android.widget.LinearLayout;
import cz.kinst.jakub.coursemanager.R;

/**
 * Base Android Activity class providing Tab functionality. Activity Content is
 * divided into several Tabs, which are represented by a button with label. When
 * user clicks on the button, tab is immediatelly switched as well as content.
 * 
 * @author Jakub Kinst
 * 
 */
public class TabbedActivity extends Activity {
	/**
	 * Color used as background on active tab button
	 */
	private static final int COLOR_ACTIVE = Color.parseColor("#cccccc");

	/**
	 * Color used as background on inactive tab button
	 */
	private static final int COLOR_NORMAL = Color.parseColor("#eeeeee");

	/**
	 * Collection of tabs
	 */
	protected ArrayList<Tab> tabs = new ArrayList<Tab>();

	/**
	 * Name of the currently visible tab
	 */
	private String activeTab;

	/**
	 * Tab content
	 */
	private LinearLayout tabContent;

	/**
	 * Tab navigation panel
	 */
	private LinearLayout tabsPanel;

	/**
	 * Header panel
	 */
	private LinearLayout header;

	/**
	 * Default constructor
	 */
	public TabbedActivity() {
	}

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		setContentView(R.layout.tabbedactivity);
		tabContent = (LinearLayout) findViewById(R.id.tabContent);
		tabsPanel = (LinearLayout) findViewById(R.id.tabs);
		header = (LinearLayout) findViewById(R.id.header);
		super.onCreate(savedInstanceState);
	}

	/**
	 * Adds tab to a tab collection
	 * 
	 * @param name
	 *            Unique tab name
	 * @param view
	 *            View with tab content
	 * @param title
	 *            Title of the tab
	 */
	public void addTab(final String name, View view, CharSequence title) {
		Tab t = new Tab(name, view, title);
		tabs.add(t);

		tabsPanel.addView(t.getButton());
		if (tabs.size() <= 1) {
			tabsPanel.setVisibility(View.GONE);
		} else {
			tabsPanel.setVisibility(View.VISIBLE);
		}
	}

	/**
	 * Adds tab to a tab collection
	 * 
	 * @param name
	 *            Unique tab name
	 * @param resourceId
	 *            View with tab content (Resource ID)
	 * @param title
	 *            Title of the tab
	 */
	public void addTab(String name, int resourceId, CharSequence title) {
		LayoutInflater inflater = (LayoutInflater) getSystemService(Context.LAYOUT_INFLATER_SERVICE);
		addTab(name, inflater.inflate(resourceId, null), title);
	}

	/**
	 * Adds special tab, which works as link - after it is clicked on, provided
	 * intent is triggered.
	 * 
	 * @param name
	 *            Unique tab name
	 * @param title
	 *            Tab title
	 * @param intent
	 *            Intent to trigger
	 */
	public void addRedirectTab(final String name, CharSequence title,
			Intent intent) {
		Tab t = new Tab(name, title, intent);
		tabs.add(t);

		tabsPanel.addView(t.getButton());
		if (tabs.size() <= 1) {
			tabsPanel.setVisibility(View.GONE);
		} else {
			tabsPanel.setVisibility(View.VISIBLE);
		}
	}

	/**
	 * Get tab by its name
	 * 
	 * @param name
	 * @return
	 */
	public View getTab(String name) {
		return getTabByName(name).getView();
	}

	/**
	 * Switches tab from one to another.
	 * 
	 * @param name
	 *            Name of the tab to switch to
	 */
	public void switchTab(String name) {
		leaveTab(activeTab);
		activeTab = name;
		Tab t = getTabByName(name);
		t.getButton().setBackgroundColor(COLOR_ACTIVE);
		tabContent.removeAllViews();
		tabContent.addView(getTab(name), new LayoutParams(
				LayoutParams.MATCH_PARENT, LayoutParams.MATCH_PARENT));
		onTabSwitched(t);
	}

	/**
	 * Override to listen to tab switch event
	 * 
	 * @param t
	 *            tab switched to
	 */
	protected void onTabSwitched(Tab t) {
	}

	/**
	 * Called when leaving tab
	 * 
	 * @param name
	 */
	private void leaveTab(String name) {
		if (getTabByName(name) != null) {
			getTabByName(name).getButton().setBackgroundColor(COLOR_NORMAL);
		}
	}

	/**
	 * returns name of currently visible tab
	 * 
	 * @return
	 */
	public String getActiveTab() {
		return activeTab;
	}

	/**
	 * Returns tab based on the provided name
	 * 
	 * @param name
	 * @return
	 */
	private Tab getTabByName(String name) {
		for (Tab tab : tabs) {
			if (tab.getName().equals(name)) {
				return tab;
			}
		}
		return null;
	}

	/**
	 * Sets View which is shown above tab navigation panel
	 * 
	 * @param resourceId
	 */
	public void setHeader(int resourceId) {
		LayoutInflater inflater = (LayoutInflater) getSystemService(Context.LAYOUT_INFLATER_SERVICE);
		header.removeAllViews();
		header.addView(inflater.inflate(resourceId, null));
	}

	/**
	 * Header getter
	 * 
	 * @return
	 */
	public View getHeader() {
		return header;
	}

	/**
	 * Class representing a Tab in TabActivity
	 * 
	 * @author Jakub Kinst
	 * 
	 */
	public class Tab {
		/**
		 * Tab name string (must be unique)
		 */
		private String name;

		/**
		 * Tab title - visible in UI
		 */
		private CharSequence title;

		/**
		 * Tab View - content
		 */
		private View view;

		/**
		 * Tab button
		 */
		private Button button;

		/**
		 * Default constructor.
		 * 
		 * @param name
		 * @param view
		 * @param title
		 */
		public Tab(final String name, View view, CharSequence title) {
			this.name = name;
			this.view = view;
			this.setTitle(title);
			this.button = new Button(TabbedActivity.this);
			this.button.setText(title);
			this.button.setBackgroundColor(COLOR_NORMAL);

			// Set onClick Event
			this.button.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					switchTab(name);
				}
			});
		}

		/**
		 * Constructor for Redirecting tab
		 * 
		 * @param name
		 * @param title
		 * @param intent
		 */
		public Tab(final String name, CharSequence title, final Intent intent) {
			this.name = name;
			this.setTitle(title);
			this.button = new Button(TabbedActivity.this);
			this.button.setText(title);
			this.button.setBackgroundColor(COLOR_NORMAL);

			// Set onClick Event
			this.button.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					startActivity(intent);
				}
			});
		}

		public String getName() {
			return name;
		}

		public View getView() {
			return view;
		}

		public Button getButton() {
			return button;
		}

		public CharSequence getTitle() {
			return title;
		}

		public void setTitle(CharSequence title) {
			this.title = title;
		}
	}
}
