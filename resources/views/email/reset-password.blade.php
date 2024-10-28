<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>

<body>
    <p>Yth. <strong>{{ $nama }}</strong>,</p>
    <p>
        Kami telah menerima permintaan untuk mereset password akun anda di Sistem Informasi Tugas Akhir dan Magang.
        Untuk melanjutkan proses reset password, silakan klik tautan di bawah ini:
    </p>
    <p>
        <a href="{{ $reset_link }}" target="_blank">Reset Password</a>
    </p>
    <p>
        Tautan tersebut akan mengarahkan anda ke halaman untuk membuat password baru.
    </p>
    <p>
        Jika anda tidak meminta reset password ini, mohon abaikan email ini. Akun anda akan tetap aman.
    </p>
    <p>
        Terima kasih telah menggunakan Sistem Informasi Tugas Akhir.
    </p>
    <p>
        Salam hormat,<br>
        Admin Sistem Informasi Tugas Akhir
    </p>
</body>

</html>
