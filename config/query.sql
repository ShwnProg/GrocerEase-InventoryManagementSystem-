CREATE DATABASE grocer_easedb;
use grocer_easedb;

CREATE TABLE users(
	user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(250) NOT NULL,
    password VARCHAR(250) NOT NULL,
    email VARCHAR(250) UNIQUE NOT NULL,
    contact_number VARCHAR(20) NOT NULL
);

CREATE TABLE categories(
	category_id_pk INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(250) NOT NULL,
    description LONGTEXT,
    is_deleted TINYINT DEFAULT 0
);
CREATE TABLE products(
	product_id_pk INT PRIMARY KEY AUTO_INCREMENT,
    product_name VARCHAR(250) NOT NULL,
    description LONGTEXT,
    selling_price DECIMAL(10,2) NOT NULL,
    cost_price DECIMAL(10,2) NOT NULL,
    status TINYINT default 1,
    is_deleted TINYINT default 0,
    category_id_fk INT NOT NULL,
    FOREIGN KEY(category_id_fk) REFERENCES categories(category_id_pk)
);
CREATE TABLE suppliers(
	supplier_id_pk INT PRIMARY KEY AUTO_INCREMENT,
    supplier_name VARCHAR(250) NOT NULL,
    contact_person VARCHAR(20) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    email VARCHAR(250) NOT NULL,
    address VARCHAR(250) NOT NULL,
    company_name VARCHAR(250) NOT NULL,
    is_deleted TINYINT default 0
);
CREATE TABLE product_supplier(
	product_supplier_id_pk INT PRIMARY KEY AUTO_INCREMENT,
    product_id_fk INT NOT NULL,
    supplier_id_fk INT NOT NULL,
    cost_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY(product_id_fk) REFERENCES products(product_id_pk),
    FOREIGN KEY(supplier_id_fk) REFERENCES suppliers(supplier_id_pk)
);

CREATE TABLE stocks(
	stock_id_pk INT PRIMARY KEY AUTO_INCREMENT,
    product_id_fk INT NOT NULL,
    quantity  BIGINT not null,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE stock_movements(
	movement_id_pk INT PRIMARY KEY AUTO_INCREMENT,
    quantity BIGINT NOT NULL,
    type enum('IN','OUT') NOT NULL,
    reference_type enum('purchase','release'),
    reference_id INT NOT NULL,
    date DATE
);

alter table stocks 
add column unit VARCHAR(250);