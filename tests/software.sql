/*
|-------------------------------------------------------------------------------
| 1. Create test database schema
|-------------------------------------------------------------------------------
*/

-- application
DROP TABLE IF EXISTS application;
CREATE TABLE application (
  id            INTEGER NOT NULL PRIMARY KEY,
  author_id     INTEGER NOT NULL,
  maintainer_id INTEGER,
  title         TEXT    NOT NULL,
  web           TEXT,
  slogan        TEXT    NOT NULL,
  FOREIGN KEY (author_id) REFERENCES author (id) ON DELETE CASCADE ON UPDATE RESTRICT,
  FOREIGN KEY (maintainer_id) REFERENCES author (id) ON DELETE SET NULL ON UPDATE RESTRICT
);
CREATE INDEX author_id ON application (author_id);
CREATE INDEX maintainer_id ON application (maintainer_id);
-- author
DROP TABLE IF EXISTS author;
CREATE TABLE author (
  id   INTEGER NOT NULL PRIMARY KEY,
  name TEXT    NOT NULL,
  web  TEXT    NOT NULL,
  born DATE
);
-- tag
DROP TABLE IF EXISTS tag;
CREATE TABLE tag (
  id   INTEGER NOT NULL PRIMARY KEY,
  name TEXT    NOT NULL
);
-- application_tag
DROP TABLE IF EXISTS application_tag;
CREATE TABLE application_tag (
  application_id INTEGER,
  tag_id         INTEGER,
  PRIMARY KEY (application_id, tag_id),
  FOREIGN KEY (application_id) REFERENCES application (id) ON DELETE CASCADE ON UPDATE RESTRICT,
  FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE RESTRICT ON UPDATE RESTRICT
);
CREATE INDEX tag_id ON application_tag (tag_id);

/*
|-------------------------------------------------------------------------------
| 2. Fill with test data
|-------------------------------------------------------------------------------
*/

-- author
INSERT INTO author (id, name, web, born) VALUES (11, 'Jakub Vrana', 'http://www.vrana.cz/', NULL);
INSERT INTO author (id, name, web, born) VALUES (12, 'David Grudl', 'http://davidgrudl.com/', NULL);
-- tag
INSERT INTO tag (id, name) VALUES (21, 'PHP');
INSERT INTO tag (id, name) VALUES (22, 'MySQL');
INSERT INTO tag (id, name) VALUES (23, 'JavaScript');
-- application
INSERT INTO application (id, author_id, maintainer_id, title, web, slogan) VALUES (1, 11, 11, 'Adminer', 'http://www.adminer.org/', 'Database management in single PHP file');
INSERT INTO application (id, author_id, maintainer_id, title, web, slogan) VALUES (2, 11, NULL, 'JUSH', 'http://jush.sourceforge.net/', 'JavaScript Syntax Highlighter');
INSERT INTO application (id, author_id, maintainer_id, title, web, slogan) VALUES (3, 12, 12, 'Nette', 'http://nettephp.com/', 'Nette Framework for PHP 5');
INSERT INTO application (id, author_id, maintainer_id, title, web, slogan) VALUES (4, 12, 12, 'Dibi', 'http://dibiphp.com/', 'Database Abstraction Library for PHP 5');
-- application_tag relations
INSERT INTO application_tag (application_id, tag_id) VALUES (1, 21);
INSERT INTO application_tag (application_id, tag_id) VALUES (3, 21);
INSERT INTO application_tag (application_id, tag_id) VALUES (4, 21);
INSERT INTO application_tag (application_id, tag_id) VALUES (1, 22);
INSERT INTO application_tag (application_id, tag_id) VALUES (4, 22);
INSERT INTO application_tag (application_id, tag_id) VALUES (2, 23);
