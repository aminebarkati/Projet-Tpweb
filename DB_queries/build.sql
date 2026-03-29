CREATE DATABASE algosparkDB;
CREATE USER 'clientalgospark'@'localhost' IDENTIFIED BY 'Algospark123!';
GRANT ALL PRIVILEGES ON algosparkDB.* TO 'clientalgospark'@'localhost';
FLUSH PRIVILEGES;      
use algosparkDB;
CREATE table users(
    id int PRIMARY KEY auto_increment,
    username VARCHAR(30) NOT NULL,
    email VARCHAR(150) NOT NULL,
    password VARCHAR(300) NOT NULL
)

select * from users;
DELETE from users;