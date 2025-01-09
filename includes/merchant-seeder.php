<?php
// merchant-seeder.php
require_once './model/Database.php';

function seedProductsForMerchant($conn, $merchantId, $productCount)
{
  if (!$conn) {
    die("Database connection is not established.");
  }

  $csvFile = __DIR__ . '/../data/lego_scrape.csv';
  if (!file_exists($csvFile)) {
    die("CSV file not found");
  }

  $fileHandle = fopen($csvFile, 'r');
  if (!$fileHandle) {
    die("Failed to open CSV file.");
  }

  fgetcsv($fileHandle); // Skip the header row

  $productsSeeded = 0;
  while (($row = fgetcsv($fileHandle)) !== false && $productsSeeded < $productCount) {
    $imgUrl = $row[0];
    $productNameParts = explode(',', $row[1]);
    $productName = trim($productNameParts[0]);

    $price = (float)str_replace(['PHP', 'php', 'Php', ','], '', $row[2]);
    $quantity = (int)str_replace(['in stock', 'In Stock'], '', $row[3]);
    $description = $row[4];

    $stmt = $conn->prepare("INSERT INTO products (merchant_id, image, name, price, quantity, description) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('issdis', $merchantId, $imgUrl, $productName, $price, $quantity, $description);
    $stmt->execute();

    $productsSeeded++;
  }

  fclose($fileHandle);

  echo "Seeder completed successfully! $productsSeeded products were added for merchant ID: $merchantId.\n";
}
