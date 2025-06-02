<?php
session_start();

if (!isset($_SESSION['last_invoice'])) {
    echo "Data invoice tidak ditemukan. Silakan lakukan checkout terlebih dahulu.";
    exit();
}

$invoice = $_SESSION['last_invoice'];

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <!-- Tambah font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: monospace;
            padding: 20px;
            max-width: 600px;
            margin: auto;
            background: #fff;
        }
        .invoice-box {
            border: 1px dashed #999;
            padding: 20px;
        }
        #brand {
            font-family: 'Poppins', sans-serif;
            color: #888;
            font-size: 20px;
            text-align: center;
            margin-bottom: 0;
        }
        #address {
            font-family: 'Poppins', sans-serif;
            color: #aaa;
            font-size: 13px;
            text-align: center;
            margin-bottom: 10px;
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
        button {
            margin: 5px 5px 5px 0;
            padding: 8px 15px;
            cursor: pointer;
            border: 1px solid #999;
            background-color: #f9f9f9;
            font-family: monospace;
        }
        button:hover {
            background-color: #eee;
        }
    </style>
</head>
<body>

<div class="invoice-box">
    <div id="brand">BuyKasir</div>
    <div id="address">Jl. Mawar No. 123, Bandung</div>
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

  <?php if ($invoice['member']): ?>
    <p>Member: <?= htmlspecialchars($invoice['member']['name']) ?> (<?= $invoice['member']['phone'] ?>)</p>
<?php endif; ?>

    <p class="text-center">Terima kasih atas pembeliannya!</p>
</div>

<!-- Tombol Aksi -->
<button onclick="downloadPDF()">Download PDF</button>

<?php if (!empty($invoice['member']['phone'])): ?>
    <button onclick="sendWhatsApp()">Kirim WhatsApp</button>
<?php endif; ?>


<a href="produk.php">
    <button>Kembali ke Produk</button>
</a>

<script>
function sendWhatsApp() {
    const invoice = <?= json_encode($invoice) ?>;
   const memberPhone = "<?= htmlspecialchars($invoice['member']['phone'] ?? '') ?>";

    if (!memberPhone) {
        alert("Nomor WhatsApp tidak tersedia.");
        return;
    }

    function formatNumber(n) {
        return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    let msg = `🧾 *Struk Pembayaran BuyKasir*\n`;
    msg += `📅 ${invoice.date}\n\n`;

    invoice.cart.forEach((item, i) => {
        const name = item.name || 'Produk';
        const price = parseInt(item.price || 0);
        const qty = parseInt(item.qty || 1);
        const subtotal = price * qty;
        msg += `${i + 1}. ${name} (${qty}x) - Rp${formatNumber(subtotal)}\n`;
    });

    msg += `\n*Subtotal:* Rp${formatNumber(invoice.total)}`;
    if (invoice.discount > 0) {
        msg += `\n*Diskon:* Rp${formatNumber(invoice.discount)}`;
    }
    msg += `\n*Total Bayar:* Rp${formatNumber(invoice.finalTotal)}`;
    msg += `\n*Dibayar:* Rp${formatNumber(invoice.payment)}`;
    if (invoice.change > 0) {
        msg += `\n*Kembali:* Rp${formatNumber(invoice.change)}`;
    }
    msg += `\n\n Terima kasih telah berbelanja🙏`;

    const phone = memberPhone.replace(/^0/, "62");
    const waLink = `https://wa.me/${phone}?text=${encodeURIComponent(msg)}`;
    window.open(waLink, '_blank');
}

// Reset local cart
localStorage.removeItem('cart');

function downloadPDF() {
    const element = document.querySelector('.invoice-box');

    const opt = {
        margin:       0,
        filename:     'invoice.pdf',
        image:        { type: 'jpeg', quality: 1 },
        html2canvas:  {
            scale: 2,
            useCORS: true,
            backgroundColor: '#ffffff',
            scrollY: 0
        },
        jsPDF: {
            unit: 'pt',
            format: [600, element.scrollHeight],
            orientation: 'portrait'
        }
    };

    html2pdf().set(opt).from(element).save();
}
</script>

</body>
</html>
