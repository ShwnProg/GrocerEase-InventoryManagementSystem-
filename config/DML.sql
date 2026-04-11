use grocer_easedb;

SELECT product_id_pk,product_name,category_name,company_name,product_cost_price,selling_price,product_description,status
FROM products as p INNER JOIN categories as c ON p.category_id_fk = c.category_id_pk
INNER JOIN product_supplier as ps ON ps.product_id_fk = p.product_id_pk
INNER JOIN suppliers as s ON ps.supplier_id_fk = s.supplier_id_pk;

SELECT * FROM products;

update products set is_deleted = 0 WHERE product_id_pk = 6;

SELECT * FROM product_supplier;

SELECT * FROM suppliers;

INSERT INTO product_supplier (product_id_fk, supplier_id_fk, cost_price)
VALUES
(6, 1, 80.00),
(9, 2, 280.00),
(10, 3, 30.00),
(11, 4, 85.00),
(12, 5, 40.00),
(13, 2, 110.00),
(14, 3, 250.00),
(15, 4, 20.00),
(16, 5, 35.00),
(17, 1, 140.00),
(18, 2, 90.00),
(19, 3, 100.00),
(20, 4, 60.00),
(21, 5, 30.00);


alter table products
drop column product_cost_price;

select * from categories;

alter table categories
change column description category_description LONGTEXT;

UPDATE categories SET  category_description = 'Fresh fruits and produce' WHERE category_id_pk = 1;
UPDATE categories SET category_description = 'Milk, cheese, and dairy products' WHERE category_id_pk = 2;
UPDATE categories SET  category_description = 'Rice, wheat, and grain products' WHERE category_id_pk= 3;
UPDATE categories SET category_description = 'Preserved and canned food items' WHERE category_id_pk = 4;
UPDATE categories SET  category_description = 'Cooking oils and related products' WHERE category_id_pk = 5;
                                                 