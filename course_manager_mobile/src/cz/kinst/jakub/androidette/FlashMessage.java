package cz.kinst.jakub.androidette;

/**
 * 
 * @author Jakub Kinst
 */
public class FlashMessage {

	String type;
	String message;

	public FlashMessage(String message, String type) {
		this.type = type;
		this.message = message;
	}

	public String getMessage() {
		return message;
	}

	public void setMessage(String message) {
		this.message = message;
	}

	public String getType() {
		return type;
	}

	public void setType(String type) {
		this.type = type;
	}
}
