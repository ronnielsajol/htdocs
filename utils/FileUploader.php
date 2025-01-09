<?php

class FileUploader
{
  private string $targetDir;
  private int $maxFileSize;
  private array $allowedFileTypes;

  public function __construct(string $targetDir, int $maxFileSize = 5000000, array $allowedFileTypes = ['jpg', 'jpeg', 'png', 'gif'])
  {
    $this->targetDir = $targetDir;
    $this->maxFileSize = $maxFileSize;
    $this->allowedFileTypes = $allowedFileTypes;

    // Ensure the directory exists
    if (!is_dir($this->targetDir)) {
      mkdir($this->targetDir, 0777, true);
    }
  }

  public function upload(array $file, int $merchantId,  $productId): string
  {
    if ($file['error'] !== UPLOAD_ERR_OK) {
      throw new Exception('File upload error code: ' . $file['error']);
    }

    // Validate file size
    if ($file['size'] > $this->maxFileSize) {
      throw new Exception('File size exceeds the maximum limit of ' . $this->maxFileSize / 1000000 . 'MB.');
    }

    // Validate file type
    $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileType, $this->allowedFileTypes)) {
      throw new Exception('Invalid file type. Allowed types: ' . implode(', ', $this->allowedFileTypes) . '.');
    }

    // Ensure the file is an actual image
    if (!getimagesize($file['tmp_name'])) {
      throw new Exception('File is not a valid image.');
    }

    // Generate a custom file name: date-merchantId-productId.ext
    $date = date('dmY'); // e.g., 01092025 for January 9, 2025
    $uniqueFileName = "{$date}-{$merchantId}-{$productId}.{$fileType}";
    $targetFile = $this->targetDir . '/' . $uniqueFileName;

    if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
      throw new Exception('Error moving uploaded file.');
    }

    return $uniqueFileName; // Return only the file name, not the full path
  }
}
