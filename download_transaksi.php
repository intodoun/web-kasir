<?php
require 'vendor/autoload.php';
require_once('connection.php');

use Dompdf\Dompdf;
use Dompdf\Options;

$filter = $_GET['filter'] ?? '';
$tanggal = $_GET['date'] ?? '';

$whereClause = 'WHERE 1=1';
$params = [];
$param_types = '';
$judul = 'Semua Transaksi';

if ($filter == 'day') {
    $whereClause = "WHERE DATE(t.date) = CURDATE()";
    $judul = 'Laporan Transaksi Hari Ini';
} elseif ($filter == 'week') {
    $whereClause = "WHERE YEARWEEK(t.date, 1) = YEARWEEK(CURDATE(), 1)";
    $judul = 'Laporan Transaksi Minggu Ini';
} elseif ($filter == 'month') {
    $whereClause = "WHERE MONTH(t.date) = MONTH(CURDATE()) AND YEAR(t.date) = YEAR(CURDATE())";
    $judul = 'Laporan Transaksi Bulan Ini';
} elseif ($filter == 'date' && !empty($tanggal)) {
    $whereClause = "WHERE DATE(t.date) = ?";
    $params[] = $tanggal;
    $param_types .= 's';
    $judul = 'Laporan Transaksi Tanggal ' . date('d-m-Y', strtotime($tanggal));
}

$query = "SELECT t.*, m.name AS member_name, a.username AS admin_username 
          FROM transactions t
          LEFT JOIN member m ON t.fid_member = m.id
          LEFT JOIN admin a ON t.fid_admin = a.id
          $whereClause
          ORDER BY t.date DESC";

$stmt = $conn->prepare($query);
if ($param_types !== "") {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
$total_penjualan = 0;
$total_keuntungan = 0;
$total_modal = 0;

while ($row = $result->fetch_assoc()) {
    $row['total_price'] = (float)($row['total_price'] ?? 0);
    $row['margin_total'] = (float)($row['margin_total'] ?? 0);
    $row['modal'] = $row['total_price'] - $row['margin_total'];

    $total_penjualan += $row['total_price'];
    $total_keuntungan += $row['margin_total'];
    $total_modal += $row['modal'];

    $rows[] = $row;
}
$stmt->close();
$conn->close();

// HTML for PDF
$html = '
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    h2 { text-align: center; }
    table { border-collapse: collapse; width: 100%; margin-top: 10px; }
    th, td { border: 1px solid #000; padding: 6px; text-align: left; }
    .total { font-weight: bold; }
</style>

<h2>' . $judul . '</h2>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Admin</th>
            <th>Member</th>
            <th>Detail</th>
            <th>Total</th>
            <th>Margin</th>
            <th>Modal</th>
        </tr>
    </thead>
    <tbody>';

$no = 1;
foreach ($rows as $row) {
    $html .= '<tr>
        <td>' . $no++ . '</td>
        <td>' . date('d-m-Y', strtotime($row['date'])) . '</td>
        <td>' . $row['admin_username'] . '</td>
        <td>' . $row['member_name'] . '</td>
        <td>' . $row['detail'] . '</td>
        <td>Rp' . number_format($row['total_price'], 0, ',', '.') . '</td>
        <td>Rp' . number_format($row['margin_total'], 0, ',', '.') . '</td>
        <td>Rp' . number_format($row['modal'], 0, ',', '.') . '</td>
    </tr>';
}

$html .= '
    </tbody>
    <tfoot>
        <tr class="total">
            <td colspan="5">Total Seluruh Transaksi</td>
            <td>Rp' . number_format($total_penjualan, 0, ',', '.') . '</td>
            <td>Rp' . number_format($total_keuntungan, 0, ',', '.') . '</td>
            <td>Rp' . number_format($total_modal, 0, ',', '.') . '</td>
        </tr>
    </tfoot>
</table>';

// DOMPDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Output PDF
$dompdf->stream("laporan_transaksi.pdf", ["Attachment" => false]); // true = download, false = view
exit;
?>
 