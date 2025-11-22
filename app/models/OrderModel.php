<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class OrderModel extends Model
{
    protected $table = 'orders';
    protected $primary_key = 'id';
}

?>
