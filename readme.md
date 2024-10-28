
<p align="center">
<a href="https://laravel.com"  target="_blank"><img  src="https://ik.polines.ac.id/wp-content/uploads/2023/11/logo-web.png"  width="360"  alt="Laravel Logo"></a> 
<a  href="https://laravel.com"  target="_blank"><img  src="https://ik.polines.ac.id/wp-content/uploads/2024/02/laravel-logo.jpg"  width="220"  alt="Laravel Logo"></a>
</p>  

# PBL Template D3 Teknik Informatika & S.Tr. Teknologi Rekayasa Komputer

Repository ini digunakan sebagai template aplikasi dasar yang akan digunakan untuk pelaksanaan <i><b>Project-Based Learning</b></i> pada kedua prodi di atas di Jurusan Teknik Elektro, Politeknik Negeri Semarang.

<i>Minimum requirements</i> untuk menjalankan template ini adalah:
- PHP 8.1
- Laravel 10
- MySQL 8.0/MariaDB 10.4

Cara menggunakan template ini adalah sebagai berikut:
1. Dengan menggunakan ``terminal`` atau ``command prompt``, duplikasi template ini menggunakan perintah:
```
git clone https://gitlab.com/sukotyasp/pbl-laravel-template.git {project-directory}
```
2. Masuk ke ``{project-directory}``, hapus folder **hidden** bernama `` .git``.
3. Alternatif selain melakukan langkah 1. dan 2., anda dapat mengunduh versi terbaru yang dipublikasikan pada link <a href='https://gitlab.com/sukotyasp/pbl-laravel-template/-/tags?sort=version_desc'>berikut</a>. Kemudian ``extract`` file yang anda unduh. Buka ``terminal`` atau ``command prompt``, lalu pilih folder hasil ekstrak sebagai folder aktif pada command line.
4. Install dependency menggunakan composer dengan perintah

```
composer install
```
5. __Copy__ file ``.env.example`` menjadi ``.env``
6. Buat database sesuai yang anda butuhkan, kemudian sesuaikan entry berikut pada file ``.env``:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE={your database}
DB_USERNAME={your database username}
DB_PASSWORD={your database password}
```
7. Jalankan perintah berikut:
```
php artisan key:generate
php artisan migrate
php artisan db:seed
```
7.5. Jalankan perintah berikut:
```
php artisan storage:link
```
8. Jalankan aplikasi menggunakan perintah:
```
php artisan serve
```
9. Anda dapat memodifikasi port yang digunakan:
```
php artisan serve --port={custom port}
```
10. Selesai, anda dapat login menggunakan:
```
username: superadmin@gmail.com
password: adminadmin
```
<hr>

Terima Kasih kepada:
- Kaprodi D3 Teknik Informatika
- Kaprodi S.Tr. Teknologi Rekayasa Komputer
- Ketua Jurusan Teknik Elektro, Politeknik Negeri Semarang
- Task Force PBL D3 Teknik Informatika & S.Tr. Teknologi Rekayasa Komputer
<hr>
Modifikasi dari Project: https://github.com/mjumain/RBAC-LARAVEL-9
