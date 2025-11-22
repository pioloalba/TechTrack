-- Fix image URLs that have /public/ in them
UPDATE product_images 
SET image_url = REPLACE(image_url, '/public/uploads/', '/uploads/') 
WHERE image_url LIKE '%/public/uploads/%';

-- Verify the fix
SELECT product_id, image_url FROM product_images WHERE product_id = 128;
