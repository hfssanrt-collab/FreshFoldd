-- Jalankan di phpMyAdmin (tab SQL) pada database FreshFold.
-- Jika tabel admins SUDAH ada, cek dulu kolomnya dengan: DESCRIBE admins;
-- Login membutuhkan kolom: username dan password (kolom id dan name/nama opsional).

CREATE TABLE IF NOT EXISTS admins (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Akun awal -> username: admin | password: admin123  (GANTI setelah login pertama!)
INSERT INTO admins (name, username, password)
VALUES ('Admin FreshFold', 'admin', '$2y$10$czczvoABI0DS0CanCgF6wO6VRqPG2Esdrm0HWCkQbBJHilJT1BjjK');
