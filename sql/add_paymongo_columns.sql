ALTER TABLE orders 
ADD COLUMN payment_intent_id VARCHAR(255) NULL AFTER payment_status,
ADD COLUMN payment_source_id VARCHAR(255) NULL AFTER payment_intent_id;
