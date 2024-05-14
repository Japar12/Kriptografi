<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Input dan Tampilan Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>``
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12 col-md-6 shadow-lg p-3 mb-5 bg-body-tertiary rounded">
                <h2 class="text-center bg-info p-2">Form Input Data</h2>
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama:</label>
                        <input type="text" class="form-control" id="nama" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label for="kelas" class="form-label">Kelas:</label>
                        <input type="text" class="form-control" id="kelas" name="kelas" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                    </div>
                    <div class="mb-3">
                        <label for="hp" class="form-label">HP:</label>
                        <input type="text" class="form-control" id="hp" name="hp" required>
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat:</label>
                        <textarea class="form-control" id="alamat" name="alamat" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" name="submit">Submit</button>
                </form>
            </div>
            <div class="col-12 col-md-6">
                <?php
                function encryptText($plainText, $key) {
                    $ivLength = openssl_cipher_iv_length('aes-256-cbc');
                    $iv = openssl_random_pseudo_bytes($ivLength);
                    $encryptedText = openssl_encrypt($plainText, 'aes-256-cbc', $key, 0, $iv);
                    $encryptedData = base64_encode($iv . $encryptedText);
                    return $encryptedData;
                }

                function decryptText($encryptedData, $key) {
                    $decodedData = base64_decode($encryptedData);
                    $ivLength = openssl_cipher_iv_length('aes-256-cbc');
                    $iv = substr($decodedData, 0, $ivLength);
                    $encryptedText = substr($decodedData, $ivLength);
                    $decryptedText = openssl_decrypt($encryptedText, 'aes-256-cbc', $key, 0, $iv);
                    return $decryptedText;
                }

                $key = "admin12345";

                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "kriptografi";
                $conn = new mysqli($servername, $username, $password, $dbname);

                if ($conn->connect_error) {
                    die("Koneksi ke database gagal: " . $conn->connect_error);
                }

                if (isset($_POST['submit'])) {
                    $nama = $_POST['nama'];
                    $kelas = $_POST['kelas'];
                    $email = $_POST['email'];
                    $hp = $_POST['hp'];
                    $alamat = $_POST['alamat'];

                    $encryptedNama = encryptText($nama, $key);
                    $encryptedKelas = encryptText($kelas, $key);
                    $encryptedEmail = encryptText($email, $key);
                    // $encryptedHp = encryptText($hp, $key); // Hapus ini
                    $encryptedHp = $hp; // Tetapkan nilai hp seperti biasa
                    $encryptedAlamat = encryptText($alamat, $key);

                    $sql = "INSERT INTO data_pengguna (nama, kelas, email, hp, alamat) VALUES ('$encryptedNama', '$encryptedKelas', '$encryptedEmail', '$encryptedHp', '$encryptedAlamat')";

                    if ($conn->query($sql) === TRUE) {
                        echo "<div class='alert alert-success'>Data berhasil disimpan.</div>";
                    } else {
                        echo "<div class='alert alert-danger'>Error: " . $sql . "<br>" . $conn->error . "</div>";
                    }
                }

                $sql = "SELECT * FROM data_pengguna";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    
                    echo "<h2 class='text-center bg-primary text-white p-2 '>Data Pengguna Terenkripsi</h2>";
                    echo "<div class='table-responsive'>";
                    echo "<table class='table table-bordered border-primary'><thead class='table-primary'><tr><th class='text-center'>No</th><th class='text-center'>Nama</th><th class='text-center'>Kelas</th><th class='text-center'>Email</th><th class='text-center'>HP</th><th class='text-center'>Alamat</th></tr></thead><tbody>";
                    $no = 1;
                    while ($row = $result->fetch_assoc()) {
                        $decryptedNama = decryptText($row["nama"], $key);
                        $decryptedKelas = decryptText($row["kelas"], $key);
                        $decryptedEmail = decryptText($row["email"], $key);
                        $decryptedHp = $row["hp"]; // Ambil nilai hp yang tidak dienkripsi
                        $decryptedAlamat = decryptText($row["alamat"], $key);
                        echo "<tr><td class='text-center'>" . $no++ . "</td><td>" . $decryptedNama . "</td><td class='text-center'>" . $decryptedKelas . "</td><td>" . $decryptedEmail . "</td><td class='text-center'>" . $decryptedHp . "</td><td>" . $decryptedAlamat . "</td></tr>";
                    }
                    echo "</tbody></table>";
                    echo "</div>";
                } else {
                    echo "<div class='alert alert-info'>Tidak ada data.</div>";
                }
                $conn->close();
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
