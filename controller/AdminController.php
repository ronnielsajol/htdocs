<?php

require_once __DIR__ . '/AdminModel.php';

class AdminController
{
  private $adminModel;

  public function __construct()
  {
    $this->adminModel = new AdminModel();
  }

  public function register($username, $password, $email)
  {
    return $this->adminModel->register($username, $password, $email);
  }

  public function login($username, $password)
  {
    return $this->adminModel->login($username, $password);
  }

  // Fetch all users
  public function getAllUsers()
  {
    return $this->adminModel->getAllUsers();
  }

  // Fetch all merchants
  public function getAllMerchants()
  {
    return $this->adminModel->getAllMerchants();
  }

  // Fetch all orders
  public function getAllOrders()
  {
    return $this->adminModel->getAllOrders();
  }

  // Fetch statistics
  public function getStatistics()
  {
    return $this->adminModel->getStatistics();
  }
}
