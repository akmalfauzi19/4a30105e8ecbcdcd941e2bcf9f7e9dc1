

<h2>Installasi </h2>
<p>aplikasi sederhana untuk mengirim email ke karyawan yang terdaftar di database dan dengan menggunakan JWT untuk authorization-nya</p>

<h2>Cara Installasi </h2>

<p>Cara install,jalankan perintah composer dibawah ini.</p>


```sh
composer install
```


Kemudian konfirgurasi file .env.example menjadi .env dan isi sesuai nama yang ada:

```sh
# Database
DB_HOST = 
DB_NAME = 
DB_USER = 
DB_PASSWORD = 

# JWT ACCESS
ACCESS_TOKEN_SECRET = 

# MAIL 
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls

```
 <p>Import file database & postman collection terlebih dahulu: </p>

```

```
untuk menjalankan ketik perintah di terminal untuk menjalankan server local :
```sh
php -S 127.0.0.1:8000
```


<h2>Cara menggunakan </h2
    <p>
setelah itu Jalankan dengan perintah :
</p>

```sh
php artisan serve
```
