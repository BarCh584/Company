DROP DATABASE IF EXISTS Company;
CREATE DATABASE Company;
USE Company;

CREATE TABLE messages (
  id INT NOT NULL AUTO_INCREMENT,
  sender TEXT NOT NULL,
  receiver TEXT NOT NULL,
  createdat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  message TEXT NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE monthlypaymentchart (
  id INT(11) NOT NULL AUTO_INCREMENT,
  subscriber VARCHAR(255) NOT NULL,
  creator VARCHAR(255) NOT NULL,
  amount FLOAT NOT NULL,
  currency TEXT NOT NULL,
  createdat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  status TEXT NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE penalties (
  id INT NOT NULL AUTO_INCREMENT,
  username VARCHAR(255) NOT NULL,
  userid INT NOT NULL,
  creator TEXT NOT NULL,
  creatorid TEXT NOT NULL,
  penalty VARCHAR(255) NOT NULL,
  reason VARCHAR(255) NOT NULL,
  createdat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  status TEXT NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE reports (
  id INT NOT NULL AUTO_INCREMENT,
  reason TEXT NOT NULL,
  applicantusername TEXT NOT NULL,
  applicantuserid INT NOT NULL,
  reportedusername TEXT NOT NULL,
  reportedcontenttitle TEXT NOT NULL,
  reportedcontent TEXT NOT NULL,
  reporteduserid INT NOT NULL,
  reportedtype TEXT NOT NULL,
  reportedcontentid INT NOT NULL,
  createdat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  status TEXT NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE users (
  id INT(10) NOT NULL AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL,
  password VARCHAR(100) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  2FA TINYINT(1) NOT NULL,
  language TEXT NOT NULL,
  darkmode TINYINT(1) NOT NULL,
  preferedcurrency TEXT NOT NULL,
  oauth_uid VARCHAR(50) NOT NULL,
  followers INT NOT NULL,
  twofa_secret VARCHAR(255) NOT NULL,
  permissions TEXT NOT NULL,
  PRIMARY KEY (id)
);