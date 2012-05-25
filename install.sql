-- mention list
DROP TABLE IF EXISTS wcf1_user_mention;
CREATE TABLE wcf1_user_mention (
	mentionID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,	
	mentionedUserID INT(10) NOT NULL,
	userID INT(10) NOT NULL DEFAULT 0, -- 0 for guests
	controller VARCHAR(255) NOT NULL,
	messageID INT(10) NOT NULL,
	messageTitle VARCHAR(255),	
	KEY (mentionedUserID)
);