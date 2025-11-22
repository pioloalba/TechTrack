<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ApiProducts extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->model('ProductModel');
    }

    public function index()
    {
        header('Content-Type: application/json');
        echo json_encode($this->ProductModel->all());
    }

    public function show($id)
    {
        header('Content-Type: application/json');
        echo json_encode($this->ProductModel->find($id));
    }

    public function store()
    {
        $id = $this->ProductModel->insert($_POST);
        header('Content-Type: application/json');
        echo json_encode(['id' => $id]);
    }

    public function update($id)
    {
        $this->ProductModel->update($id, $_POST);
        header('Content-Type: application/json');
        echo json_encode(['updated' => true]);
    }

    public function delete($id)
    {
        $this->ProductModel->delete($id);
        header('Content-Type: application/json');
        echo json_encode(['deleted' => true]);
    }
}

?>
