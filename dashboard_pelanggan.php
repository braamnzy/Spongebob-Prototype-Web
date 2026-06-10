<?php
require_once 'core/auth.php';
cekLogin();
if ($_SESSION['role'] === 'karyawan' || $_SESSION['role'] === 'admin') {
    header("Location: dashboard_karyawan.php");
    exit;
}
require_once 'core/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pesan'])) {
    $catatan        = bersihkanInput($_POST['catatan']);
    $nama_pelanggan = $_SESSION['username'];
    $total_harga    = 0;
    $pesanan_items  = [];

    if (isset($_POST['menu'])) {
        foreach ($_POST['menu'] as $id => $item) {
            if ($item['jumlah'] > 0) {
                $id  = (int) $id;
                $res = mysqli_query($conn, "SELECT nama, harga FROM menu WHERE id = $id");
                if ($row = mysqli_fetch_assoc($res)) {
                    $total_harga  += $row['harga'] * (int) $item['jumlah'];
                    $pesanan_items[] = [
                        'tipe'         => 'menu',
                        'item_id'      => $id,
                        'nama_item'    => $row['nama'],
                        'harga_satuan' => $row['harga'],
                        'jumlah'       => (int) $item['jumlah'],
                    ];
                }
            }
        }
    }

    if (isset($_POST['merch'])) {
        foreach ($_POST['merch'] as $id => $item) {
            if ($item['jumlah'] > 0) {
                $id  = (int) $id;
                $res = mysqli_query($conn, "SELECT nama, harga FROM merchandise WHERE id = $id");
                if ($row = mysqli_fetch_assoc($res)) {
                    $total_harga  += $row['harga'] * (int) $item['jumlah'];
                    $pesanan_items[] = [
                        'tipe'         => 'merchandise',
                        'item_id'      => $id,
                        'nama_item'    => $row['nama'],
                        'harga_satuan' => $row['harga'],
                        'jumlah'       => (int) $item['jumlah'],
                    ];
                }
            }
        }
    }

    if ($total_harga > 0) {
        $q = "INSERT INTO pesanan (nama_pelanggan, catatan, total_harga, status) VALUES ('$nama_pelanggan', '$catatan', $total_harga, 'menunggu')";
        if (mysqli_query($conn, $q)) {
            $pesanan_id = mysqli_insert_id($conn);
            foreach ($pesanan_items as $pi) {
                mysqli_query($conn, "INSERT INTO pesanan_detail (pesanan_id, tipe, item_id, nama_item, harga_satuan, jumlah)
                                     VALUES ($pesanan_id, '{$pi['tipe']}', {$pi['item_id']}, '{$pi['nama_item']}', {$pi['harga_satuan']}, {$pi['jumlah']})");
            }
            echo "<script>alert('Pesanan berhasil dibuat! Menunggu diproses.'); window.location='dashboard_pelanggan.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Pilih minimal satu pesanan!');</script>";
    }
}

$menu_list  = getAllData('menu');
$merch_list = getAllData('merchandise');

$q_pesanan_saya   = "SELECT * FROM pesanan WHERE nama_pelanggan = '{$_SESSION['username']}' ORDER BY id DESC LIMIT 5";
$res_pesanan_saya = mysqli_query($conn, $q_pesanan_saya);
$pesanan_saya     = [];
if ($res_pesanan_saya) {
    while ($r = mysqli_fetch_assoc($res_pesanan_saya)) {
        $pesanan_saya[] = $r;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - The Krusty Krab</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
    <script>
        function updateCount(inputId, delta) {
            const input = document.getElementById(inputId);
            let val = parseInt(input.value);
            if (isNaN(val)) val = 0;
            val += delta;
            if (val < 0) val = 0;
            input.value = val;
            calculateTotal();
        }

        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.count-display').forEach(input => {
                const price = parseInt(input.getAttribute('data-price')) || 0;
                const count = parseInt(input.value) || 0;
                total += price * count;
            });
            document.getElementById('live-total-display').innerText = 'Total Bayar: Rp ' + total.toLocaleString('id-ID');
        }
    </script>
</head>
<body class="dashboard-page">

    <nav class="navbar">
        <a href="switch_role.php" title="Toggle Employee/Customer Role">
            <img src="public/img/kk_logo.png" alt="Logo" class="nav-logo">
        </a>
        <div class="nav-divider"></div>
        <a href="logout.php" class="nav-icon" title="Logout" onclick="return confirm('Yakin ingin keluar?')">
            <img src="public/img/logout 1.png" alt="Logout">
        </a>
        <div class="nav-divider"></div>
        <a href="home.php" class="nav-icon" title="Home">
            <img src="public/img/checkout 1.png" alt="Home">
        </a>
    </nav>

    <div class="main-content">
        <div class="card">

            <?php if (!empty($pesanan_saya)): ?>
            <div class="info-tab">
                <h4>Status Pesanan Terbaru Anda:</h4>
                <ul style="list-style: none; padding-left: 0; margin-top: 10px;">
                    <?php foreach ($pesanan_saya as $ps): ?>
                        <li class="pesanan-item">
                            <span>Pesanan #<?= $ps['id'] ?> (Rp <?= number_format($ps['total_harga'], 0, ',', '.') ?>)</span>
                            <span class="badge-status status-<?= strtolower($ps['status']) ?>"><?= htmlspecialchars($ps['status']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <div id="live-total-display" class="live-total">Total Bayar: Rp 0</div>

            <form action="dashboard_pelanggan.php" method="POST">

                <h3>Krusty Krab's Menu</h3>
                <div class="img-grid">
                    <div class="img-item"><img src="public/img/burger.jpg" alt="Burger"></div>
                    <div class="img-item"><img src="public/img/sandwich.jpg" alt="Sandwich"></div>
                    <div class="img-item"><img src="public/img/pizza.jpg" alt="Pizza"></div>
                    <div class="img-item"><img src="public/img/spaghetti.webp" alt="Spaghetti"></div>
                    <div class="img-item"><img src="public/img/fishandchips.webp" alt="Fish and Chips"></div>
                    <div class="img-item"><img src="public/img/soup.jpg" alt="Soup"></div>
                    <div class="img-item"><img src="public/img/steak.webp" alt="Steak"></div>
                    <div class="img-item"><img src="public/img/macandcheese.jpg" alt="Mac & Cheese"></div>
                </div>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>ID Menu</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Count</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($menu_list)): ?>
                                <tr><td colspan="5">Tidak ada menu.</td></tr>
                            <?php else: ?>
                                <?php foreach ($menu_list as $m): ?>
                                <tr>
                                    <td><?= htmlspecialchars(sprintf("MN%03d", $m['id'])) ?></td>
                                    <td>
                                        <?= htmlspecialchars($m['nama']) ?>
                                        <br><small><?= htmlspecialchars($m['deskripsi']) ?></small>
                                    </td>
                                    <td>Rp <?= number_format($m['harga'], 0, ',', '.') ?></td>
                                    <td>
                                        <input type="number" name="menu[<?= $m['id'] ?>][jumlah]" id="menu_<?= $m['id'] ?>"
                                               value="0" min="0" class="count-display" data-price="<?= $m['harga'] ?>" readonly>
                                    </td>
                                    <td>
                                        <div class="action-cell">
                                            <button type="button" class="action-btn btn-plus"  onclick="updateCount('menu_<?= $m['id'] ?>', 1)">+</button>
                                            <button type="button" class="action-btn btn-minus" onclick="updateCount('menu_<?= $m['id'] ?>', -1)">-</button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <h3>Krusty Krab's Merchandise</h3>
                <div class="img-grid merch-grid">
                    <div class="img-item"><img src="public/img/tshirt.jpg" alt="T-Shirt"></div>
                    <div class="img-item"><img src="public/img/shirt.jpg" alt="Shirt"></div>
                    <div class="img-item"><img src="public/img/hat.jpg" alt="Hat"></div>
                    <div class="img-item"><img src="public/img/key.webp" alt="Keychain"></div>
                    <div class="img-item"><img src="public/img/backpack.jpg" alt="Backpack"></div>
                </div>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>ID Merch</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Count</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($merch_list)): ?>
                                <tr><td colspan="5">Tidak ada merchandise.</td></tr>
                            <?php else: ?>
                                <?php foreach ($merch_list as $m): ?>
                                <tr>
                                    <td><?= htmlspecialchars(sprintf("MC%03d", $m['id'])) ?></td>
                                    <td>
                                        <?= htmlspecialchars($m['nama']) ?>
                                        <br><small><?= htmlspecialchars($m['deskripsi']) ?></small>
                                    </td>
                                    <td>Rp <?= number_format($m['harga'], 0, ',', '.') ?></td>
                                    <td>
                                        <input type="number" name="merch[<?= $m['id'] ?>][jumlah]" id="merch_<?= $m['id'] ?>"
                                               value="0" min="0" class="count-display" data-price="<?= $m['harga'] ?>" readonly>
                                    </td>
                                    <td>
                                        <div class="action-cell">
                                            <button type="button" class="action-btn btn-plus"  onclick="updateCount('merch_<?= $m['id'] ?>', 1)">+</button>
                                            <button type="button" class="action-btn btn-minus" onclick="updateCount('merch_<?= $m['id'] ?>', -1)">-</button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="form-section">
                    <h3>Orders Data</h3>
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" value="<?= htmlspecialchars($_SESSION['username']) ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Notes (Catatan Pesanan)</label>
                        <textarea name="catatan" rows="3" placeholder="Masukkan catatan tambahan..."></textarea>
                    </div>
                    <div class="action-buttons">
                        <a href="dashboard_pelanggan.php" class="btn btn-cancel">Cancel</a>
                        <button type="submit" name="pesan" class="btn btn-save">Save</button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <footer>
        <nav class="footer-nav">
            <a href="home.php">Home</a>
            <a href="dashboard_pelanggan.php">Orders</a>
            <a href="logout.php" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
        </nav>
        <div class="footer-info">
            <p>Operational Time:</p>
            <div class="hours">09.00 - 20.00</div>
            <div class="social-icons">
                <a href="#" title="Instagram">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                    </svg>
                </a>
                <a href="#" title="YouTube">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 0 0 1.46 6.42 29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58 2.78 2.78 0 0 0 1.95 1.96C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.96A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/>
                        <polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"/>
                    </svg>
                </a>
                <a href="#" title="X (Twitter)">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                </a>
            </div>
        </div>
        <div class="footer-logo">
            <img src="public/img/kk_logo.png" alt="The Krusty Krab">
        </div>
    </footer>

</body>
</html>
