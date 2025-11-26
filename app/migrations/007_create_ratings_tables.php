<?php
class Create_ratings_tables
{
    private $_lava;
    protected $dbforge;

    public function __construct()
    {
        $this->_lava =& lava_instance();
        $this->_lava->call->dbforge();
        $this->dbforge = $this->_lava->dbforge;
    }

    public function up()
    {
        // product_ratings table - stores individual customer ratings
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ],
            'product_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ],
            'customer_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
                'comment' => 'NULL for guest ratings, INT for authenticated customers'
            ],
            'session_id' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
                'null' => TRUE,
                'comment' => 'Track guest ratings by session'
            ],
            'rating' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => FALSE,
                'comment' => '1-5 stars'
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => FALSE,
                'default' => 'CURRENT_TIMESTAMP'
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE,
                'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
            ]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('product_ratings', TRUE);

        // Add indexes for performance
        $this->_lava->db->raw("CREATE INDEX idx_product_ratings_product ON product_ratings(product_id)");
        $this->_lava->db->raw("CREATE INDEX idx_product_ratings_customer ON product_ratings(customer_id)");
        $this->_lava->db->raw("CREATE INDEX idx_product_ratings_session ON product_ratings(session_id)");
        $this->_lava->db->raw("CREATE UNIQUE INDEX idx_product_ratings_unique ON product_ratings(product_id, customer_id, session_id)");

        // product_reviews table - stores detailed text reviews
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ],
            'product_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ],
            'customer_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE
            ],
            'rating_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE,
                'comment' => 'Links to product_ratings table'
            ],
            'customer_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => TRUE,
                'comment' => 'Display name for review'
            ],
            'review_title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE
            ],
            'review_text' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'verified_purchase' => [
                'type' => 'BOOLEAN',
                'default' => FALSE,
                'comment' => 'TRUE if customer bought this product'
            ],
            'helpful_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Number of helpful votes'
            ],
            'status' => [
                'type' => 'ENUM("pending","approved","rejected")',
                'default' => 'approved',
                'comment' => 'Moderation status'
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => FALSE,
                'default' => 'CURRENT_TIMESTAMP'
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE,
                'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
            ]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('product_reviews', TRUE);

        // Add indexes
        $this->_lava->db->raw("CREATE INDEX idx_product_reviews_product ON product_reviews(product_id)");
        $this->_lava->db->raw("CREATE INDEX idx_product_reviews_customer ON product_reviews(customer_id)");
        $this->_lava->db->raw("CREATE INDEX idx_product_reviews_rating ON product_reviews(rating_id)");
        $this->_lava->db->raw("CREATE INDEX idx_product_reviews_status ON product_reviews(status)");

        echo "✓ Created product_ratings table with indexes\n";
        echo "✓ Created product_reviews table with indexes\n";
    }

    public function down()
    {
        $this->dbforge->drop_table('product_reviews', TRUE);
        $this->dbforge->drop_table('product_ratings', TRUE);
        echo "✓ Dropped product_reviews table\n";
        echo "✓ Dropped product_ratings table\n";
    }
}
