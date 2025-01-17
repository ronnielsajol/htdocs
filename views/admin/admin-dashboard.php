<?php
include './views/shared/admin-header.php';
require_once './model/AdminModel.php';

$model = new AdminModel();

// Fetch data
$users = $model->getAllUsers();
$merchants = $model->getAllMerchants();
$orders = $model->getAllOrders();
$statistics = $model->getStatistics();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="/css/admin.css">
</head>

<body>
  <main class="admin-dashboard">
    <!-- Statistics Section -->
    <section>
      <h2>Statistics</h2>
      <ul>
        <li>Total Users: <?= $statistics['total_users'] ?></li>
        <li>Total Merchants: <?= $statistics['total_merchants'] ?></li>
        <li>Total Orders: <?= $statistics['total_orders'] ?></li>
        <li>Total Revenue: <?= number_format($statistics['total_revenue'], 2) ?></li>
      </ul>
    </section>

    <!-- Users Table -->
    <section>
      <h2>All Users</h2>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $user): ?>
            <tr>
              <td><?= $user['id'] ?></td>
              <td><?= htmlspecialchars($user['username']) ?></td>
              <td><?= htmlspecialchars($user['email']) ?></td>
              <td><?= htmlspecialchars($user['role']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>

    <!-- Merchants Table -->
    <section>
      <h2>All Merchants</h2>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($merchants as $merchant): ?>
            <tr>
              <td><?= $merchant['id'] ?></td>
              <td><?= htmlspecialchars($merchant['username']) ?></td>
              <td><?= htmlspecialchars($merchant['email']) ?></td>
              <td><?= htmlspecialchars($merchant['role']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>

    <!-- Orders Table -->
    <section>
      <h2>All Orders</h2>
      <table>
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Transaction Number</th>
            <th>Order Date</th>
            <th>Total Amount</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $order): ?>
            <tr>
              <td><?= $order['id'] ?></td>
              <td><?= $order['transaction_number'] ?></td>
              <td><?= htmlspecialchars($order['created_at']) ?></td>
              <td><?= number_format($order['total_amount'], 2) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>
  </main>
</body>

<?php include './views/shared/footer.php'; ?>

</html>