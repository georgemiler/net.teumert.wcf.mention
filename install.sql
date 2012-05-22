-- mention list
DROP TABLE IF EXISTS wcf1_user_mention;
CREATE TABLE wcf1_user_mention (
	mentionID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	userID INT(10) NOT NULL,
	mentionUserID INT(10) NOT NULL,
	objectType VARCHAR(255) NOT NULL,
	objectID INT(10) NOT NULL
	KEY (mentionUserID)
);

-- TODO FOREIGN KEYS?