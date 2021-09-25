# database config

create table recipes
(
    id      int auto_increment primary key,
    name    varchar(255),
    slug    varchar(255),
    picture varchar(255),
    body    text
) engine InnoDB
  charset = utf8mb4;

create table ingredients
(
    id int auto_increment primary key,
    name varchar(255),
    barcode varchar(255),
    quantity double,
    recipe_id int,
    foreign key recipe_key (recipe_id) references recipes (id)
) engine InnoDB
  charset = utf8mb4;

