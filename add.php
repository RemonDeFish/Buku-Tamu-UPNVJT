<style>
    body { 
        background-color: #3E2723; 
        display: flex; 
        justify-content: center; 
        align-items: center; 
        height: 100vh;
        margin: 0;
    }
    .container { 
        width: 350px; 
        padding: 30px; 
        background-color: #F5DEB3; /* Warna kertas lama */
        border: 10px double #8B0000;
        border-radius: 4px;
        box-shadow: 10px 10px 5px #000;
    }
    h3 { color: #8B0000; text-align: center; font-family: 'Palatino', serif; }
    input { 
        width: 100%; padding: 10px; margin: 10px 0; 
        border: 1px solid #8B0000; background: transparent;
        border-bottom: 2px solid #8B0000;
    }
    button { 
        width: 100%; background-color: #8B0000; color: #D4AF37; 
        padding: 14px; border: none; font-weight: bold; cursor: pointer;
    }
    button:hover { background-color: #D4AF37; color: #8B0000; }
    .logo-placeholder {
        width: 80px; height: 80px; margin: 0 auto 20px;
        background: url('link_logo_surya_majapahit.png') no-repeat center; /* PLACEHOLDER LOGO */
        background-size: contain;
    }
</style>

<div class="container">
    <div class="logo-placeholder"></div>
    <h3>Pendaftaran Mahasiswa</h3>
    <form action="add.php" method="POST">
        <label>Nama Lengkap</label>
        <input type="text" name="nama" required>
        <label>NPM</label>
        <input type="text" name="npm" required>
        <button type="submit" name="submit">Simpan ke Arsip Kerajaan</button>
    </form>
    <a href="index.php" style="color: #8B0000; display: block; text-align: center; margin-top: 15px;">← Kembali</a>
</div>