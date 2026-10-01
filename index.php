<!-- File utama yang mengarahkan ke halaman login. -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Inventori Santan Ardafa</title>
    <link rel="stylesheet" href="assets/css/style.css?v=1.6">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2E7D32;
            --bg-dark: #E8F5E9;
            /* Mint Cream */
            --bg-accent: #E8F5E9;
            --text-main: #333333;
            --text-muted: #546E7A;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-accent);
            color: var(--text-main);
            overflow: hidden;
            margin: 0;
            height: 100vh;
            display: flex;
        }

        .left-panel {
            width: 45%;
            height: 100vh;
            /* Green Gradient */
            background: linear-gradient(135deg, #66BB6A 0%, #2E7D32 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 0 60px;
            z-index: 10;
        }

        /* The Curve Shape */
        .left-panel::after {
            content: '';
            position: absolute;
            top: 0;
            right: -100px;
            width: 200px;
            height: 100%;
            background: linear-gradient(135deg, #66BB6A 0%, #2E7D32 100%);
            /* Match panel */
            border-radius: 0 50% 50% 0 / 0 50% 50% 0;
            z-index: -1;
        }

        /* Decor Circle */
        .decor-circle {
            position: absolute;
            top: -50px;
            left: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 248, 240, 0.15);
            /* Warm tint */
            border-radius: 50%;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 40px;
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 4rem;
            font-weight: 800;
            line-height: 1.1;
            margin: 0 0 20px;
            color: white;
            text-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        p {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.9);
            margin: 0 0 40px;
            max-width: 400px;
            line-height: 1.6;
        }

        .btn-group {
            display: flex;
            gap: 15px;
        }

        .btn {
            padding: 14px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 1rem;
        }

        .btn-white {
            background: white;
            color: #2E7D32;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .btn-white:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .btn-outline-white {
            border: 2px solid rgba(255, 255, 255, 0.4);
            color: white;
        }

        .btn-outline-white:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: white;
        }


        /* RIGHT PANEL (Illustration) */
        .right-panel {
            flex: 1;
            height: 100vh;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('assets/images/toko_sembako_hd.jpg') no-repeat center center;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding-left: 100px;
            /* Offset for curve */
        }



        /* Mobile Responsive */
        @media (max-width: 900px) {
            body {
                flex-direction: column;
                overflow-y: auto;
                height: auto;
            }

            .left-panel {
                width: 100%;
                height: auto;
                padding: 60px 30px 100px;
                border-radius: 0 0 50px 50px;
            }

            .left-panel::after {
                display: none;
            }

            .right-panel {
                width: 100%;
                height: 500px;
                padding: 40px;
            }

            h1 {
                font-size: 3rem;
            }

            .landing-logo {
                max-width: 60%;
                padding-left: 0;
            }
        }
    </style>
</head>

<body>

    <!-- LEFT PANEL -->
    <div class="left-panel">
        <div class="decor-circle"></div>

        <div class="brand-logo">
            <i class="fas fa-cube"></i> Santan Ardafa
        </div>

        <h1>Manage Your<br>Store Easier.</h1>
        <p>Sistem inventori dan kasir modern untuk membantu bisnis UMKM Anda tumbuh lebih cepat dan efisien.</p>

        <div class="btn-group">
            <a href="auth/login.php" class="btn btn-white">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="right-panel">

    </div>

</body>

</html>