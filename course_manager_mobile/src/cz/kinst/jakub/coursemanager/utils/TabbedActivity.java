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

public class TabbedActivity extends Activity {
	private static final int COLOR_ACTIVE = Color.parseColor("#cccccc");
	private static final int COLOR_NORMAL = Color.parseColor("#eeeeee");
	protected ArrayList<Tab> tabs = new ArrayList<Tab>();
	private String activeTab;
	private LinearLayout tabContent;
	private LinearLayout tabsPanel;
	private LinearLayout header;

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

	public void addTab(final String name, View view, CharSequence title) {
		Tab t = new Tab(name, view, title);
		tabs.add(t);

		tabsPanel.addView(t.getButton());
		if (tabs.size() <= 1)
			tabsPanel.setVisibility(View.GONE);
		else
			tabsPanel.setVisibility(View.VISIBLE);
	}

	public void addRedirectTab(final String name, CharSequence title,
			Intent intent) {
		Tab t = new Tab(name, title, intent);
		tabs.add(t);

		tabsPanel.addView(t.getButton());
		if (tabs.size() <= 1)
			tabsPanel.setVisibility(View.GONE);
		else
			tabsPanel.setVisibility(View.VISIBLE);
	}

	public void addTab(String name, int resourceId, CharSequence title) {
		LayoutInflater inflater = (LayoutInflater) getSystemService(Context.LAYOUT_INFLATER_SERVICE);
		addTab(name, inflater.inflate(resourceId, null), title);
	}

	public View getTab(String name) {
		return getTabByName(name).getView();
	}

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

	protected void onTabSwitched(Tab t) {
	}

	private void leaveTab(String name) {
		if (getTabByName(name) != null)
			getTabByName(name).getButton().setBackgroundColor(COLOR_NORMAL);
	}

	public String getActiveTab() {
		return activeTab;
	}

	private Tab getTabByName(String name) {
		for (Tab tab : tabs) {
			if (tab.getName().equals(name))
				return tab;
		}
		return null;
	}

	public void setHeader(int resourceId) {
		LayoutInflater inflater = (LayoutInflater) getSystemService(Context.LAYOUT_INFLATER_SERVICE);
		header.removeAllViews();
		header.addView(inflater.inflate(resourceId, null));
	}

	public View getHeader() {
		return header;
	}

	public class Tab {
		private String name;
		private CharSequence title;
		private View view;
		private Button button;

		public Tab(final String name, View view, CharSequence title) {
			this.name = name;
			this.view = view;
			this.setTitle(title);
			this.button = new Button(TabbedActivity.this);
			this.button.setText(title);
			this.button.setBackgroundColor(COLOR_NORMAL);
			this.button.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					switchTab(name);
				}
			});
		}

		public Tab(final String name, CharSequence title, final Intent intent) {
			this.name = name;
			this.setTitle(title);
			this.button = new Button(TabbedActivity.this);
			this.button.setText(title);
			this.button.setBackgroundColor(COLOR_NORMAL);
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
