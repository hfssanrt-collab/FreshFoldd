<?php
require_once 'auth.php';
require_once 'koneksi.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

function h($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

$error    = '';
$info     = isset($_GET['logout']) ? 'Anda berhasil keluar.' : '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        try {
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare('SELECT * FROM admins WHERE username = ? LIMIT 1');
            $stmt->execute([$username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            $stored = $admin ? (string)($admin['password'] ?? '') : '';
            // Password ter-hash (bcrypt) diverifikasi dengan password_verify.
            // Password lama yang masih teks biasa tetap bisa dipakai, sebaiknya segera di-hash.
            $isHash = $stored !== '' && !empty(password_get_info($stored)['algo']);
            $ok = $admin && ($isHash
                ? password_verify($password, $stored)
                : ($stored !== '' && hash_equals($stored, $password)));

            if ($ok) {
                session_regenerate_id(true);
                $_SESSION['admin_id']   = $admin['id'] ?? $admin['username'];
                $_SESSION['admin_name'] = $admin['name'] ?? $admin['full_name'] ?? $admin['nama'] ?? $admin['username'];
                header('Location: index.php');
                exit;
            }

            usleep(500000); // perlambat tebak-tebakan password
            $error = 'Username atau password salah.';
        } catch (Throwable $e) {
            $error = 'Tidak bisa memeriksa akun. Pastikan tabel admins sudah ada (kolom: username, password).';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Masuk — FreshFold</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --green: #176b55;
            --dark: #10513f;
            --soft: #e8f5ef;
            --ink: #20332b;
            --muted: #7c8982;
            --line: #e7ede9;
            --bg: #f7f9f7;
            --red: #c05e4e
        }

        * { box-sizing: border-box }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            background: var(--bg);
            color: var(--ink);
            font-family: 'DM Sans', sans-serif
        }

        .hero {
            background: linear-gradient(125deg, #125640, #208064);
            color: white;
            padding: 48px;
            display: flex;
            flex-direction: column;
            justify-content: space-between
        }

        .brand { display: flex; align-items: center; gap: 12px }

        .brand-mark {
            width: 46px;
            height: 46px;
            border-radius: 15px;
            background: rgba(255, 255, 255, .16);
            display: grid;
            place-items: center;
            font-size: 24px
        }

        .brand-name { font: 800 24px Manrope, sans-serif; letter-spacing: -.8px }
        .brand-name span { color: #a8e3c9 }

        .hero h2 {
            font: 800 36px/1.2 Manrope, sans-serif;
            letter-spacing: -1px;
            margin: 0 0 14px;
            max-width: 420px
        }

        .hero p { color: #d3e9df; font-size: 14px; line-height: 1.7; max-width: 400px; margin: 0 }
        .hero small { color: #a9cfc0; font-size: 12px }
        .emoji { font-size: 90px; margin-bottom: 18px }

        .panel-wrap { display: grid; place-items: center; padding: 28px }

        .card {
            width: min(100%, 400px);
            background: white;
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 8px 28px #203e3010
        }

        .card h1 { font: 800 26px Manrope, sans-serif; letter-spacing: -.8px; margin: 0 0 6px }
        .sub { font-size: 13px; color: var(--muted); margin: 0 0 22px; line-height: 1.6 }

        .alert {
            border-radius: 10px;
            padding: 11px 13px;
            font-size: 12px;
            margin-bottom: 16px;
            line-height: 1.5
        }

        .alert.error { background: #fff0ed; color: var(--red); border: 1px solid #f3d3cc }
        .alert.info { background: #e8f5ed; color: #28764e; border: 1px solid #cfe8d9 }

        .group { margin-bottom: 16px }
        label { display: block; font-size: 11px; font-weight: 700; color: #53625a; margin-bottom: 8px }

        .field {
            width: 100%;
            border: 1px solid #e1e9e3;
            background: white;
            border-radius: 10px;
            padding: 13px;
            color: var(--ink);
            outline: none;
            font: inherit;
            font-size: 13px
        }

        .field:focus { border-color: #83b59b; box-shadow: 0 0 0 3px #176b5510 }

        .pw { position: relative }
        .pw .field { padding-right: 70px }

        .toggle {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            color: var(--green);
            font: inherit;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            padding: 6px 8px
        }

        .btn {
            width: 100%;
            border: 0;
            border-radius: 10px;
            background: var(--green);
            color: white;
            padding: 14px;
            font: inherit;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 5px 14px #176b5520;
            margin-top: 6px
        }

        .btn:hover { background: var(--dark) }

        .foot { text-align: center; font-size: 11px; color: var(--muted); margin-top: 20px }

        @media(max-width:850px) {
            body { grid-template-columns: 1fr }
            .hero { display: none }
        }
    </style>
</head>

<body>
    <section class="hero">
        <div class="brand">
            <div class="brand-mark">✦</div>
            <div class="brand-name">fresh<span>fold</span></div>
        </div>
        <div>
            <div class="emoji">🧺</div>
            <h2>Pakaian bersih, hati tenang.</h2>
            <p>Masuk untuk mengelola pesanan, pelanggan, layanan, dan laporan laundry kamu dalam satu tempat.</p>
        </div>
        <small>© <?= date('Y') ?> FreshFold Laundry Management</small>
    </section>

    <section class="panel-wrap">
        <div class="card">
            <h1>Masuk Admin</h1>
            <p class="sub">Silakan login untuk melanjutkan ke dashboard.</p>

            <?php if ($error): ?>
                <div class="alert error"><?= h($error) ?></div>
            <?php elseif ($info): ?>
                <div class="alert info"><?= h($info) ?></div>
            <?php endif; ?>

            <form method="post" autocomplete="on">
                <div class="group">
                    <label for="username">Username</label>
                    <input class="field" id="username" name="username" value="<?= h($username) ?>" required autofocus autocomplete="username" placeholder="Masukkan username">
                </div>
                <div class="group">
                    <label for="password">Password</label>
                    <div class="pw">
                        <input class="field" id="password" name="password" type="password" required autocomplete="current-password" placeholder="Masukkan password">
                        <button type="button" class="toggle" id="toggle">Lihat</button>
                    </div>
                </div>
                <button class="btn" type="submit">Masuk</button>
            </form>

            <div class="foot">Hanya untuk admin FreshFold</div>
        </div>
    </section>

    <script>
        document.getElementById('toggle').addEventListener('click', function () {
            const input = document.getElementById('password');
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            this.textContent = show ? 'Sembunyi' : 'Lihat';
        });
    </script>
</body>

</html>
