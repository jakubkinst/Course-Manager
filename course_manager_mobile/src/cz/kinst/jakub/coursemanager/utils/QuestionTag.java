package cz.kinst.jakub.coursemanager.utils;

/**
 * Utility class. Represents question in an online assignment Defines type, id,
 * value/values
 * 
 * @author Jakub Kinst
 * 
 */
public class QuestionTag {

	String id;
	String type;
	String value;
	String[] values;

	/**
	 * Constructor takes question ID, Type, Value
	 * 
	 * @param id
	 * @param type
	 * @param value
	 */
	public QuestionTag(String id, String type, String value) {
		this.id = id;
		this.type = type;
		this.value = value;
	}

	/**
	 * Constructor takes question ID, Type, Values in array
	 * 
	 * @param id
	 * @param type
	 * @param values
	 */
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
