package cz.kinst.jakub.coursemanager.utils;

public class QuestionTag {

	String id;
	String type;
	String value;
	String[] values;

	public QuestionTag(String id, String type, String value) {
		this.id = id;
		this.type = type;
		this.value = value;
	}

	public QuestionTag(String id, String type, String[] values) {
		this.id = id;
		this.type = type;
		this.values = values;
	}

	public String getId() {
		return id;
	}

	public String getType() {
		return type;
	}

	public String getValue() {
		return value;
	}

	public String[] getValues() {
		return values;
	}

	public void setValue(String value) {
		this.value = value;
	}

	public void setValues(String[] values) {
		this.values = values;
	}

}
