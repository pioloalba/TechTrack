-- TechTrack Reports Metrics Query
-- This query calculates Total Revenue, Total Orders, and Average Order Value
-- matching the dashboard logic (all orders, no status filter)

SELECT 
    -- Total Revenue from all orders
    ROUND(SUM(total), 2) as total_revenue,
    
    -- Total number of orders
    COUNT(id) as total_orders,
    
    -- Average Order Value (Revenue / Orders)
    ROUND(SUM(total) / COUNT(id), 2) as average_order_value,
    
    -- Additional useful metrics
    ROUND(MIN(total), 2) as min_order_value,
    ROUND(MAX(total), 2) as max_order_value,
    
    -- Count by status
    SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
    SUM(CASE WHEN status = 'shipped' THEN 1 ELSE 0 END) as shipped_orders,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
    SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing_orders,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders
FROM 
    orders;

-- Alternative query: Only completed orders (Delivered + Shipped)
SELECT 
    ROUND(SUM(total), 2) as total_revenue,
    COUNT(id) as total_orders,
    ROUND(SUM(total) / COUNT(id), 2) as average_order_value
FROM 
    orders
WHERE 
    status IN ('delivered', 'shipped');

-- Monthly comparison query
SELECT 
    'Current Month' as period,
    ROUND(SUM(total), 2) as revenue,
    COUNT(id) as orders
FROM orders
WHERE MONTH(created_at) = MONTH(NOW()) 
  AND YEAR(created_at) = YEAR(NOW())

UNION ALL

SELECT 
    'Last Month' as period,
    ROUND(SUM(total), 2) as revenue,
    COUNT(id) as orders
FROM orders
WHERE MONTH(created_at) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))
  AND YEAR(created_at) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH));
