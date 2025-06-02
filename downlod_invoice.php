<?php
session_start();
require 'vendor/autoload.php';
include 'connection.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_SESSION['last_invoice'])) {
    echo "Data invoice tidak ditemukan. Silakan lakukan checkout terlebih dahulu.";
    exit();
}

$invoice = $_SESSION['last_invoice'];
$member = $invoice['member'] ?? null;

$invoice['cart'] = $invoice['cart'] ?? [];
$invoice['total'] = $invoice['total'] ?? 0;
$invoice['discount'] = $invoice['discount'] ?? 0;
$invoice['finalTotal'] = $invoice['finalTotal'] ?? 0;
$invoice['payment'] = $invoice['payment'] ?? 0;
$invoice['change'] = $invoice['change'] ?? 0;

// Ambil HTML invoice
ob_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice PDF</title>
    <style>
        body {
            font-family: monospace;
            padding: 20px;
            width: 600px;
            margin: auto;
            background: #fff;
        }
        .invoice-box {
            border: 1px dashed #999;
            padding: 20px;
        }
        .invoice-box h2 {
            text-align: center;
        }
        .line {
            border-top: 1px dashed #999;
            margin: 10px 0;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

<div class="invoice-box">
    <h2>STRUK PEMBAYARAN</h2>
    <p class="text-center"><?= htmlspecialchars($invoice['date']) ?></p>
    <div class="line"></div>

    <?php foreach ($invoice['cart'] as $item): ?>
        <?php
        $name = htmlspecialchars($item['name'] ?? 'Produk');
        $price = intval($item['price'] ?? 0);
        $qty = intval($item['qty'] ?? 1);
        $subtotal = $price * $qty;
        ?>
        <p><?= $name ?> (<?= $qty ?> x Rp<?= number_format($price, 0, ',', '.') ?>):
        <span class="text-right">Rp <?= number_format($subtotal, 0, ',', '.') ?></span></p>
    <?php endforeach; ?>

    <div class="line"></div>
    <p>Total: Rp <?= number_format($invoice['total'], 0, ',', '.') ?></p>
    <p>Diskon: Rp <?= number_format($invoice['discount'], 0, ',', '.') ?></p>
    <p><strong>Total Bayar: Rp <?= number_format($invoice['finalTotal'], 0, ',', '.') ?></strong></p>
    <p>Uang Tunai: Rp <?= number_format($invoice['payment'], 0, ',', '.') ?></p>
    <p>Kembalian: Rp <?= number_format($invoice['change'], 0, ',', '.') ?></p>
    <div class="line"></div>

    <?php if ($member): ?>
        <p>Member: <?= htmlspecialchars($member['name']) ?> (<?= htmlspecialchars($member['phone']) ?>)</p>
    <?php endif; ?>

    <p class="text-center">Terima kasih atas pembeliannya!</p>
</div>

</body>
</html>

<?php
$html = ob_get_clean();

// Inisialisasi Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'monospace');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);

// Ukuran kertas: lebar 600pt (sama dengan CSS max-width:600px), tinggi 1000pt (disesuaikan dengan isi)
$dompdf->setPaper([0, 0, 600, 1000], 'portrait');
$dompdf->render();

// Tampilkan PDF di browser (inline)
$dompdf->stream("invoice.pdf", ["Attachment" => false]);
exit;
