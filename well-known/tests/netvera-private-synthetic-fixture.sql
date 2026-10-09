-- SYNTHETIC QA ONLY. No real customer's credentials, purchases, or source SQL dump.
CREATE TABLE customers (
 id INT UNSIGNED PRIMARY KEY, name VARCHAR(120), email VARCHAR(190),
 phone VARCHAR(30), is_agency TINYINT, want_dealer TINYINT,
 password_hash VARCHAR(255), email_verified_at DATETIME NULL,
 created_at DATETIME, is_active TINYINT, status VARCHAR(20)
);
CREATE TABLE admin_users (
 id INT UNSIGNED PRIMARY KEY, name VARCHAR(120), email VARCHAR(190),
 password_hash VARCHAR(255), role VARCHAR(30), is_active TINYINT
);
CREATE TABLE orders (
 id INT UNSIGNED PRIMARY KEY, order_no VARCHAR(40) NOT NULL,
 customer_id INT UNSIGNED NULL, customer_name VARCHAR(140) NULL,
 customer_email VARCHAR(190) NULL, customer_phone VARCHAR(30) NULL,
 product_id INT UNSIGNED NULL, product_name VARCHAR(190) NULL,
 amount DECIMAL(10,2), currency VARCHAR(8), payment_status VARCHAR(25),
 order_status VARCHAR(25), order_type VARCHAR(25),
 entitlements_json LONGTEXT NULL, license_key VARCHAR(255) NULL,
 paid_at DATETIME NULL, created_at DATETIME
);
CREATE TABLE order_items (
 id INT UNSIGNED PRIMARY KEY, order_id INT UNSIGNED, item_type VARCHAR(20),
 item_id INT UNSIGNED, item_name VARCHAR(190), item_slug VARCHAR(190),
 sale_price DECIMAL(10,2), entitlements_json LONGTEXT NULL
);
CREATE TABLE affiliate_accounts (
 id BIGINT UNSIGNED PRIMARY KEY, customer_id INT UNSIGNED,
 referral_code VARCHAR(32), status VARCHAR(25), commission_rate DECIMAL(5,2) NULL
);
CREATE TABLE affiliate_commissions (
 id BIGINT UNSIGNED PRIMARY KEY, affiliate_id BIGINT UNSIGNED,
 order_id BIGINT UNSIGNED, amount DECIMAL(10,2), rate DECIMAL(5,2),
 status VARCHAR(25), paid_at DATETIME NULL, created_at DATETIME
);
CREATE TABLE support_tickets (
 id BIGINT UNSIGNED PRIMARY KEY, customer_id INT UNSIGNED NULL,
 subject VARCHAR(300), initial_message LONGTEXT, related_order_id BIGINT NULL,
 priority VARCHAR(25), status VARCHAR(25), created_at DATETIME, updated_at DATETIME
);
CREATE TABLE support_ticket_replies (
 id BIGINT UNSIGNED PRIMARY KEY, ticket_id BIGINT UNSIGNED,
 sender_type VARCHAR(30), message LONGTEXT, created_at DATETIME
);
CREATE TABLE affiliate_clicks (
 id BIGINT UNSIGNED PRIMARY KEY, affiliate_id BIGINT UNSIGNED,
 visitor_token CHAR(64), landing_path VARCHAR(500), clicked_at DATETIME
);
CREATE TABLE order_referrals (
 order_id BIGINT UNSIGNED PRIMARY KEY, affiliate_id BIGINT UNSIGNED,
 visitor_token CHAR(64), attributed_at DATETIME
);
INSERT INTO customers VALUES
(10,'QA Buyer','qa-buyer@example.test','0000000000',0,1,'SET_BCRYPT_IN_WORKFLOW','2026-01-01 00:00:00','2026-01-01 00:00:00',1,'active');
INSERT INTO admin_users VALUES
(1,'QA Imported Admin','qa-admin@example.test','SET_BCRYPT_IN_WORKFLOW','super_admin',1);
INSERT INTO orders VALUES
(70,'QA-70',10,'QA Buyer','qa-buyer@example.test','0000000000',11,'QA Licensed Script',400,'TRY','paid','completed','script','{"access":"granted"}','QA-LICENSE-NOT-REAL','2026-01-02 00:00:00','2026-01-02 00:00:00'),
(71,'QA-71',NULL,'QA Guest','qa-guest@example.test',NULL,11,'QA Guest Script',350,'TRY','pending','pending','script',NULL,NULL,NULL,'2026-01-03 00:00:00'),
(72,'QA-72',10,'QA Buyer','qa-buyer@example.test',NULL,11,'QA Rejected Script',500,'TRY','failed','cancelled','script','{"access":"not_granted"}',NULL,NULL,'2026-01-04 00:00:00');
INSERT INTO order_items VALUES (210,70,'script',11,'QA Licensed Script','qa-licensed-script',400,'{"access":"granted"}'),(211,71,'script',11,'QA Guest Script','qa-guest-script',350,NULL);
INSERT INTO affiliate_accounts VALUES (6,10,'QA-REF-CODE','approved',5);
INSERT INTO affiliate_commissions VALUES (7,6,70,20,5,'paid','2026-01-05 00:00:00','2026-01-02 00:00:00');
INSERT INTO support_tickets VALUES (20,10,'QA Support','Please test migration',70,'medium','closed','2026-01-03 00:00:00','2026-01-04 00:00:00');
INSERT INTO support_ticket_replies VALUES (21,20,'admin','QA Response','2026-01-04 00:00:00');
INSERT INTO affiliate_clicks VALUES (22,6,REPEAT('a',64),'/qa','2026-01-01 00:00:00');
INSERT INTO order_referrals VALUES (70,6,REPEAT('b',64),'2026-01-02 00:00:00');
