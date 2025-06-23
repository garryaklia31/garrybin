<?php
function generateCard($bin, $mm, $yy, $cvv) {
    $card = '';
    for ($i = 0; $i < strlen($bin); $i++) {
        $card .= $bin[$i] === 'x' ? rand(0, 9) : $bin[$i];
    }
    $month = $mm === 'rnd' ? str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) : $mm;
    $year = $yy === 'rnd' ? rand(24, 30) : $yy;
    $cvv = $cvv === 'rnd' ? rand(100, 999) : $cvv;
    return "$card|$month|$year|$cvv";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bin = $_POST['bin'];
    $mm = $_POST['mm'];
    $yy = $_POST['yy'];
    $cvv = $_POST['cvv'];
    $qty = (int)$_POST['qty'];

    $results = [];

    if (!preg_match('/^[0-9x]{12,16}$/', $bin) || $qty < 1 || $qty > 1000) {
        die("Invalid input.");
    }

    for ($i = 0; $i < $qty; $i++) {
        $results[] = generateCard($bin, $mm, $yy, $cvv);
    }

    if ($qty > 50) {
        $filename = "cards_" . time() . ".txt";
        file_put_contents($filename, implode("\n", $results));
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        readfile($filename);
        unlink($filename);
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Card Generator</title>
    <style>
        body { font-family: sans-serif; background: #f8f9fa; padding: 40px; text-align: center; }
        input, select { padding: 10px; margin: 5px; width: 200px; }
        button { padding: 12px 25px; background: #007bff; color: white; border: none; border-radius: 6px; cursor: pointer; }
        .result { margin-top: 30px; font-family: monospace; white-space: pre; background: #fff; padding: 20px;
                  border: 1px solid #ccc; display: inline-block; text-align: left; }
        footer { margin-top: 40px; font-size: 16px; color: #555; }
    </style>
</head>
<body>
<h2>🔧 Card Generator</h2>
<form method="POST">
    <input type="text" name="bin" placeholder="BIN (e.g. 526809xxxxxxxxxx)" required><br>
    <input type="text" name="mm" placeholder="MM (e.g. 01 or rnd)" required><br>
    <input type="text" name="yy" placeholder="YY (e.g. 25 or rnd)" required><br>
    <input type="text" name="cvv" placeholder="CVV (e.g. 123 or rnd)" required><br>
    <input type="number" name="qty" placeholder="Amount (1–1000)" required><br>
    <button type="submit">Generate</button>
</form>
<?php if (!empty($results) && count($results) <= 50): ?>
    <div class="result"><?= implode("\n", $results) ?></div>
<?php endif; ?>
<footer>Made by @Batchecker</footer>
</body>
</html>