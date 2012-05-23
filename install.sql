-- mention list
DROP TABLE IF EXISTS wcf1_user_mention;
CREATE TABLE wcf1_user_mention (
	mentionID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	userID INT(10) NOT NULL,
	mentionedUserID INT(10) NOT NULL,
	messageTitle VARCHAR(255),
	messageURL VARCHAR(255), -- this might be too small?
	KEY (mentionedUserID)
);