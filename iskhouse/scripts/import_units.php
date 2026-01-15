<?php
require_once __DIR__ . '/../config.php';

$inputPath = $argv[1] ?? (__DIR__ . '/../data/import_units.json');
if (!file_exists($inputPath)) {
  fwrite(STDERR, "JSON file not found: {$inputPath}\n");
  exit(1);
}

$json = file_get_contents($inputPath);
$data = json_decode($json, true);
if (!is_array($data)) {
  fwrite(STDERR, "Invalid JSON.\n");
  exit(1);
}

$branchId = (int)($data['branch_id'] ?? 0);
$floors = $data['floors'] ?? null;
if ($branchId <= 0 || !is_array($floors)) {
  fwrite(STDERR, "Missing branch_id or floors array.\n");
  exit(1);
}

$floorSelect = $link->prepare('SELECT fid FROM tbl_add_floor WHERE branch_id = ? AND floor_no = ? LIMIT 1');
$floorInsert = $link->prepare('INSERT INTO tbl_add_floor (floor_no, branch_id) VALUES (?, ?)');
$unitSelect = $link->prepare('SELECT uid FROM tbl_add_unit WHERE branch_id = ? AND floor_no = ? AND unit_no = ? LIMIT 1');
$unitInsert = $link->prepare('INSERT INTO tbl_add_unit (floor_no, unit_no, branch_id, status) VALUES (?, ?, ?, 0)');

if (!$floorSelect || !$floorInsert || !$unitSelect || !$unitInsert) {
  fwrite(STDERR, "Failed to prepare statements.\n");
  exit(1);
}

$insertedFloors = 0;
$insertedUnits = 0;
$skippedUnits = 0;

foreach ($floors as $floor) {
  $floorNo = trim((string)($floor['floor_no'] ?? ''));
  $units = $floor['units'] ?? [];
  if ($floorNo === '' || !is_array($units)) {
    continue;
  }

  $floorSelect->bind_param('is', $branchId, $floorNo);
  $floorSelect->execute();
  $floorSelect->store_result();

  $floorId = null;
  if ($floorSelect->num_rows === 0) {
    $floorInsert->bind_param('si', $floorNo, $branchId);
    $floorInsert->execute();
    if ($floorInsert->affected_rows > 0) {
      $insertedFloors++;
      $floorId = $floorInsert->insert_id;
    }
  } else {
    $floorSelect->bind_result($floorId);
    $floorSelect->fetch();
  }
  $floorSelect->free_result();

  if (!$floorId) {
    continue;
  }

  foreach ($units as $unitNoRaw) {
    $unitNo = trim((string)$unitNoRaw);
    if ($unitNo === '') {
      continue;
    }

    $unitSelect->bind_param('iis', $branchId, $floorId, $unitNo);
    $unitSelect->execute();
    $unitSelect->store_result();
    if ($unitSelect->num_rows === 0) {
      $unitInsert->bind_param('isi', $floorId, $unitNo, $branchId);
      $unitInsert->execute();
      $insertedUnits += ($unitInsert->affected_rows > 0) ? 1 : 0;
    } else {
      $skippedUnits++;
    }
    $unitSelect->free_result();
  }
}

echo "Import complete. Floors inserted: {$insertedFloors}. Units inserted: {$insertedUnits}. Units skipped: {$skippedUnits}.\n";
