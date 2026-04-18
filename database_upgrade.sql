ALTER TABLE temples ADD COLUMN image_url VARCHAR(255) DEFAULT NULL AFTER image_theme;
ALTER TABLE pujas ADD COLUMN image_url VARCHAR(255) DEFAULT NULL AFTER image_theme;
ALTER TABLE products ADD COLUMN image_url VARCHAR(255) DEFAULT NULL AFTER image_theme;
ALTER TABLE sevas ADD COLUMN image_url VARCHAR(255) DEFAULT NULL AFTER image_theme;

ALTER TABLE orders
    ADD COLUMN gateway VARCHAR(40) DEFAULT NULL AFTER payment_status,
    ADD COLUMN receipt_code VARCHAR(60) DEFAULT NULL AFTER gateway,
    ADD COLUMN razorpay_order_id VARCHAR(80) DEFAULT NULL AFTER receipt_code,
    ADD COLUMN razorpay_payment_id VARCHAR(80) DEFAULT NULL AFTER razorpay_order_id,
    ADD COLUMN razorpay_signature VARCHAR(255) DEFAULT NULL AFTER razorpay_payment_id,
    ADD COLUMN gateway_payload LONGTEXT DEFAULT NULL AFTER razorpay_signature,
    ADD COLUMN updated_at DATETIME DEFAULT NULL AFTER created_at;

ALTER TABLE puja_bookings
    ADD COLUMN gateway VARCHAR(40) DEFAULT NULL AFTER payment_status,
    ADD COLUMN receipt_code VARCHAR(60) DEFAULT NULL AFTER gateway,
    ADD COLUMN razorpay_order_id VARCHAR(80) DEFAULT NULL AFTER receipt_code,
    ADD COLUMN razorpay_payment_id VARCHAR(80) DEFAULT NULL AFTER razorpay_order_id,
    ADD COLUMN razorpay_signature VARCHAR(255) DEFAULT NULL AFTER razorpay_payment_id,
    ADD COLUMN gateway_payload LONGTEXT DEFAULT NULL AFTER razorpay_signature,
    ADD COLUMN updated_at DATETIME DEFAULT NULL AFTER created_at;

ALTER TABLE seva_bookings
    ADD COLUMN gateway VARCHAR(40) DEFAULT NULL AFTER payment_status,
    ADD COLUMN receipt_code VARCHAR(60) DEFAULT NULL AFTER gateway,
    ADD COLUMN razorpay_order_id VARCHAR(80) DEFAULT NULL AFTER receipt_code,
    ADD COLUMN razorpay_payment_id VARCHAR(80) DEFAULT NULL AFTER razorpay_order_id,
    ADD COLUMN razorpay_signature VARCHAR(255) DEFAULT NULL AFTER razorpay_payment_id,
    ADD COLUMN gateway_payload LONGTEXT DEFAULT NULL AFTER razorpay_signature,
    ADD COLUMN updated_at DATETIME DEFAULT NULL AFTER created_at;
