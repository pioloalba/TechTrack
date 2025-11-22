<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class OrderItemModel extends Model
{
    protected $table = 'order_items';
    protected $primary_key = 'id';
}

?>
