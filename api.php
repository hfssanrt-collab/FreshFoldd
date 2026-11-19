<?php
require_once 'auth.php';
require_once 'koneksi.php';
header('Content-Type: application/json');
require_login_api(); // semua aksi API hanya untuk admin yang sudah login

// Mencegah error PHP tampil sebagai HTML di JSON
error_reporting(0);
ini_set('display_errors', 0);

$action = $_GET['action'] ?? '';

if ($action === 'get_data') {
    try {
        // 1. Ambil data pesanan (gunakan LEFT JOIN agar tidak crash saat tabel customers kosong)
        $stmtOrders = $pdo->query("
            SELECT o.id, o.order_code as id_str, 
                   COALESCE(c.name, 'Pelanggan') as name, 
                   COALESCE(c.phone, '-') as phone, 
                   COALESCE(s.service_name, 'Layanan') as service, 
                   o.weight, o.total_price as total, o.order_date as date, 
                   o.estimated_completion as due, o.status, 
                   COALESCE(p.payment_status, 'Belum Lunas') as payment
            FROM orders o
            LEFT JOIN customers c ON o.customer_id = c.id
            LEFT JOIN services s ON o.service_id = s.id
            LEFT JOIN payments p ON p.order_id = o.id
            ORDER BY o.id DESC
        ");
        $orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

        // 2. Ambil data layanan
        $stmtServices = $pdo->query("SELECT * FROM services");
        $services = $stmtServices->fetchAll(PDO::FETCH_ASSOC);

        // 3. Ambil data karyawan
        $stmtEmployees = $pdo->query("SELECT * FROM employees");
        $employees = $stmtEmployees->fetchAll(PDO::FETCH_ASSOC);

        // 4. Ambil data pelanggan
        $stmtCustomers = $pdo->query("SELECT * FROM customers ORDER BY name ASC");
        $customers = $stmtCustomers->fetchAll(PDO::FETCH_ASSOC);            

        echo json_encode([
            'success' => true,
            'orders' => $orders ?: [],
            'services' => $services ?: [],
            'employees' => $employees ?: [],
            'customers' => $customers ?: []
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'orders' => [],
            'employees' => []
        ]);
    }
    exit;
}

if ($action === 'add_order' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = (int)($_POST['customer_id'] ?? 0);
    $service_id  = (int)($_POST['service_id'] ?? 1);
    $weight = $_POST['weight'] ?? 0;
    $date   = $_POST['date'] ?? date('Y-m-d');
    $due    = $_POST['due'] ?? date('Y-m-d');
    $total  = $_POST['total'] ?? 0;
    $order_code = 'LD-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

    if ($customer_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Pelanggan belum dipilih']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        $stmtO = $pdo->prepare("INSERT INTO orders (order_code, customer_id, service_id, weight, total_price, order_date, estimated_completion, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Menunggu Proses')");
        $stmtO->execute([$order_code, $customer_id, $service_id, $weight, $total, $date, $due]);
        $order_id = $pdo->lastInsertId();

        $stmtP = $pdo->prepare("INSERT INTO payments (order_id, amount_paid, payment_status) VALUES (?, 0, 'Belum Lunas')");
        $stmtP->execute([$order_id]);

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => "Pesanan $order_code berhasil dibuat!", 'order_code' => $order_code]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
if ($action === 'add_customer' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');

    if ($name === '' || $phone === '') {
        echo json_encode(['success' => false, 'message' => 'Nama dan nomor HP wajib diisi']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO customers (name, phone) VALUES (?, ?)");
        $stmt->execute([$name, $phone]);
        echo json_encode(['success' => true, 'message' => "Pelanggan $name berhasil ditambahkan!"]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
if ($action === 'get_coa') {
    try {
        $stmt = $pdo->query("
            SELECT * FROM coa
            ORDER BY kode_akun ASC
        ");

        echo json_encode([
            'success' => true,
            'coa' => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}
if ($action === 'add_coa' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode  = trim($_POST['kode_akun'] ?? '');
    $nama  = trim($_POST['nama_akun'] ?? '');
    $kat   = $_POST['kategori'] ?? '';
    $saldo = $_POST['saldo_normal'] ?? '';

    if ($kode === '' || $nama === '') {
        echo json_encode(['success' => false, 'message' => 'Kode dan nama akun wajib diisi']);
        exit;
    }
    if (!in_array($kat, ['Aset', 'Kewajiban', 'Ekuitas', 'Pendapatan', 'Beban'], true) ||
        !in_array($saldo, ['Debit', 'Kredit'], true)) {
        echo json_encode(['success' => false, 'message' => 'Kategori atau saldo normal tidak valid']);
        exit;
    }

    try {
        $cek = $pdo->prepare("SELECT COUNT(*) FROM coa WHERE kode_akun = ?");
        $cek->execute([$kode]);
        if ($cek->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => "Kode akun $kode sudah dipakai"]);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO coa (kode_akun, nama_akun, kategori, saldo_normal) VALUES (?, ?, ?, ?)");
        $stmt->execute([$kode, $nama, $kat, $saldo]);
        echo json_encode(['success' => true, 'message' => "Akun $kode berhasil ditambahkan!"]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'update_coa' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $old   = trim($_POST['old_kode_akun'] ?? '');
    $kode  = trim($_POST['kode_akun'] ?? '');
    $nama  = trim($_POST['nama_akun'] ?? '');
    $kat   = $_POST['kategori'] ?? '';
    $saldo = $_POST['saldo_normal'] ?? '';

    if ($old === '' || $kode === '' || $nama === '') {
        echo json_encode(['success' => false, 'message' => 'Kode dan nama akun wajib diisi']);
        exit;
    }
    if (!in_array($kat, ['Aset', 'Kewajiban', 'Ekuitas', 'Pendapatan', 'Beban'], true) ||
        !in_array($saldo, ['Debit', 'Kredit'], true)) {
        echo json_encode(['success' => false, 'message' => 'Kategori atau saldo normal tidak valid']);
        exit;
    }

    try {
        // Jika kode diubah, pastikan tidak bentrok dengan akun lain
        if ($kode !== $old) {
            $cek = $pdo->prepare("SELECT COUNT(*) FROM coa WHERE kode_akun = ?");
            $cek->execute([$kode]);
            if ($cek->fetchColumn() > 0) {
                echo json_encode(['success' => false, 'message' => "Kode akun $kode sudah dipakai"]);
                exit;
            }
        }

        $stmt = $pdo->prepare("UPDATE coa SET kode_akun = ?, nama_akun = ?, kategori = ?, saldo_normal = ? WHERE kode_akun = ?");
        $stmt->execute([$kode, $nama, $kat, $saldo, $old]);
        echo json_encode(['success' => true, 'message' => "Akun $kode berhasil diperbarui!"]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'delete_coa' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = trim($_POST['kode_akun'] ?? '');

    if ($kode === '') {
        echo json_encode(['success' => false, 'message' => 'Kode akun tidak valid']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM coa WHERE kode_akun = ?");
        $stmt->execute([$kode]);
        echo json_encode(['success' => true, 'message' => "Akun $kode berhasil dihapus!"]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Akun tidak bisa dihapus: ' . $e->getMessage()]);
    }
    exit;
}

if ($action === 'update_order' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = (int)($_POST['order_id'] ?? 0);
    $status   = $_POST['status'] ?? '';
    $pay      = $_POST['payment_status'] ?? '';

    if ($order_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Pesanan tidak valid']);
        exit;
    }
    if (!in_array($status, ['Menunggu Proses', 'Sedang Diproses', 'Selesai', 'Sudah Diambil'], true) ||
        !in_array($pay, ['Belum Lunas', 'Lunas'], true)) {
        echo json_encode(['success' => false, 'message' => 'Status tidak valid']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        $stmtT = $pdo->prepare("SELECT order_code, total_price FROM orders WHERE id = ?");
        $stmtT->execute([$order_id]);
        $order = $stmtT->fetch(PDO::FETCH_ASSOC);
        if (!$order) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan']);
            exit;
        }

        $stmtO = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmtO->execute([$status, $order_id]);

        $amount = $pay === 'Lunas' ? $order['total_price'] : 0;

        $cek = $pdo->prepare("SELECT COUNT(*) FROM payments WHERE order_id = ?");
        $cek->execute([$order_id]);
        if ($cek->fetchColumn() > 0) {
            $stmtP = $pdo->prepare("UPDATE payments SET amount_paid = ?, payment_status = ? WHERE order_id = ?");
            $stmtP->execute([$amount, $pay, $order_id]);
        } else {
            $stmtP = $pdo->prepare("INSERT INTO payments (order_id, amount_paid, payment_status) VALUES (?, ?, ?)");
            $stmtP->execute([$order_id, $amount, $pay]);
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => "Pesanan {$order['order_code']} berhasil diperbarui!"]);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
