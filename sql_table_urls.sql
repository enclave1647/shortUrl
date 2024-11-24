CREATE TABLE urls (
  id int(11) PRIMARY KEY AUTO_INCREMENT,
  origin_url varchar(255) UNIQUE NOT NULL,
  short_url varchar(7) UNIQUE NOT NULL
);