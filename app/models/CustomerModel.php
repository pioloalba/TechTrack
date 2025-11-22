<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class CustomerModel extends Model
{
    protected $table = 'customers';
    protected $primary_key = 'id';
}

?>
