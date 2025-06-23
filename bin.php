<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$bin = $_GET['bin'] ?? '';
$bin = substr($bin, 0, 6);

if (!preg_match('/^[0-9]{6}$/', $bin)) {
    echo json_encode(["error" => "Invalid BIN"]);
    exit;
}

try {
    $db = new PDO("sqlite:bins.db");
    $stmt = $db->prepare("SELECT brand AS scheme, type, category AS brand, issuer AS bank, alpha_2 AS country FROM bins WHERE bin = ?");
    $stmt->execute([$bin]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        echo json_encode(["error" => "BIN not found"]);
    } else {
        $result['bin'] = $bin;
        $result['checked_by'] = "@Batchecker";
        echo json_encode($result, JSON_PRETTY_PRINT);
    }

} catch (PDOException $e) {
    echo json_encode(["error" => "Database error", "message" => $e->getMessage()]);
}
?>