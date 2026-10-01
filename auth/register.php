<!-- Halaman antarmuka untuk pendaftaran pengguna baru. -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun | Santan Ardafa</title>

    <link rel="stylesheet" href="../../assets/css/style.css?v=1.6">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: #4CAF50;
            --primary-dark: #2E7D32;
            --white: #FFFFFF;
            --text-dark: #333333;
            --text-muted: #666666;
        }

        body {
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Ubuntu', sans-serif;
            position: relative;
            background: url('../../assets/images/toko_sembako_hd.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(8px);
            z-index: 1;
        }

        .auth-container {
            display: flex;
            width: 100%;
            max-width: 850px;
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 10;
            min-height: 550px;
        }

        .auth-info {
            flex: 1;
            background: linear-gradient(rgba(46, 125, 50, 0.85), rgba(46, 125, 50, 0.85)), url('../../assets/images/toko_sembako_hd.jpg') no-repeat center center;
            background-size: cover;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .auth-info h1 {
            font-size: 2.2rem !important;
            font-weight: 800;
            line-height: 1.2;
            color: white;
            text-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            z-index: 2;
        }

        .auth-info p {
            font-size: 1rem;
            margin-top: 15px;
            opacity: 0.9;
            z-index: 2;
            max-width: 280px;
        }

        .auth-info img {
            max-width: 140px;
            margin-top: 30px;
            filter: drop-shadow(0 5px 15px rgba(0, 0, 0, 0.2));
            z-index: 2;
        }

        .auth-form {
            flex: 1.2;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-form h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 5px;
        }

        .auth-form p.subtitle {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 25px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 6px;
            color: var(--text-muted);
        }

        .input-group {
            position: relative;
        }

        .input-group input,
        .input-group select {
            width: 100%;
            padding: 10px 12px;
            padding-right: 40px;
            /* Space for eye icon */
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s;
            outline: none;
            background: #fafafa;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            cursor: pointer;
            transition: color 0.3s;
            z-index: 5;
        }

        .password-toggle.active {
            color: var(--primary);
        }

        .password-toggle:hover {
            color: var(--primary-dark);
        }

        .input-group input:focus,
        .input-group select:focus {
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }

        .auth-btn {
            background: var(--primary-dark);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
            width: 100%;
        }

        .auth-btn:hover {
            background: #1B5E20;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .auth-footer {
            margin-top: 20px;
            text-align: center;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .auth-footer a {
            color: var(--primary-dark);
            text-decoration: none;
            font-weight: 700;
        }

        .alert-box {
            padding: 8px 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-danger {
            background: #FFEBEE;
            color: #D32F2F;
            border: 1px solid #FFCDD2;
        }

        .alert-success {
            background: #E8F5E9;
            color: #2E7D32;
            border: 1px solid #C8E6C9;
        }

        @media (max-width: 768px) {
            .auth-container {
                flex-direction: column;
                max-width: 450px;
                margin: 20px;
                overflow-y: auto;
            }

            .auth-info {
                padding: 25px;
            }

            .auth-info h1 {
                font-size: 1.4rem;
            }

            .auth-info img {
                max-width: 100px;
                margin-top: 15px;
            }

            .auth-form {
                padding: 25px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="auth-container">
        <!-- Left Column: Info & Logo -->
        <div class="auth-info">
            <h1>Wujudkan Visi <br> Bisnis Anda</h1>
            <p>Mulai perjalanan sukses bersama ekosistem cerdas Santan Ardafa.</p>
        </div>

        <!-- Right Column: Form -->
        <div class="auth-form">
            <h2>Daftar Akun</h2>
            <p class="subtitle">Lengkapi formulir untuk registrasi pengguna baru.</p>

            <!-- Alerts -->
            <?php if (isset($_GET['pesan'])): ?>
                <div
                    class="alert-box <?php echo ($_GET['pesan'] == 'gagal' || $_GET['pesan'] == 'duplikat' || $_GET['pesan'] == 'owner_exists') ? 'alert-danger' : 'alert-success'; ?>">
                    <?php
                    if ($_GET['pesan'] == 'gagal') {
                        echo "<i class='fas fa-exclamation-circle'></i> Registrasi gagal!";
                    } else if ($_GET['pesan'] == 'duplikat') {
                        echo "<i class='fas fa-user-times'></i> Username sudah digunakan!";
                    } else if ($_GET['pesan'] == 'owner_exists') {
                        echo "<i class='fas fa-ban'></i> <b>Akses Ditolak!</b> Role Owner sudah terdaftar dalam sistem.";
                    }
                    ?>
                </div>
            <?php endif; ?>

            <form method="post" action="proses_register.php">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <div class="input-group">
                        <input type="text" name="nama" required placeholder="Nama Lengkap Anda">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Username</label>
                        <div class="input-group">
                            <input type="text" name="username" required placeholder="Userunik">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Akses Sebagai</label>
                        <div class="input-group">
                            <select name="role" required>
                                <option value="" disabled selected>Pilih Role</option>
                                <option value="kasir">Staff Kasir</option>
                                <option value="owner">Owner / Pemilik</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" required
                            placeholder="Buat password minimal 6 karakter">
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                    </div>
                </div>

                <button type="submit" class="auth-btn">
                    Daftar Sekarang
                </button>
            </form>

            <div class="auth-footer">
                <p><span style="color:var(--primary); font-weight:700;">Santan Ardafa</span> ✨ Excellence in Every
                    Transaction</p>
                <p style="margin-top: 5px; opacity:0.7;">© 2026 Crafted with Passion</p>
                <p style="margin-top: 10px;">Sudah punya akun? <a href="login.php">Login Disini</a></p>
                <div style="margin-top:10px;">
                    <a href="../index.php" style="color:#94a3b8; font-size:0.75rem; text-decoration:none;"><i
                            class="fas fa-arrow-left"></i> Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            // Toggle icons and active color
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
            this.classList.toggle('active');
        });
    </script>
</body>

</html>