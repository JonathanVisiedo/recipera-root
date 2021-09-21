# database config

CREATE TABLE recipes
(
    id      INT AUTO_INCREMENT PRIMARY KEY,
    name    VARCHAR(255),
    slug    varchar(255),
    barcode VARCHAR(255),
    body    TEXT
) ENGINE InnoDB
  CHARSET = utf8mb4;

CREATE TABLE ingredients
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name varchar(255),
    quantity float,
    recipe_id INT,
    FOREIGN KEY recipe_key (recipe_id) REFERENCES recipes (id)
) ENGINE InnoDB
  CHARSET = utf8mb4;

