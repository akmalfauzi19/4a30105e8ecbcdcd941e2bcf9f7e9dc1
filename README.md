

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
<p>example .env :</p>

![env](https://user-images.githubusercontent.com/59783691/154884554-286d77da-d3c6-4603-a9ae-2843a4befc5f.PNG)


```

```
 <p>Import file database & postman collection terlebih dahulu: </p>

![postman](https://user-images.githubusercontent.com/59783691/154883892-959a4c10-ace6-40fe-b17f-31dc5e0539b9.PNG)
```

```
untuk menjalankan ketik perintah di terminal untuk menjalankan server local :
```sh
php -S 127.0.0.1:8000
```


<h2>Cara Menjalankan </h2

<p> Pertama register terlebh dahulu </p>

 ![register](https://user-images.githubusercontent.com/59783691/154883131-92b42e0f-4da6-4eac-a027-fb45b0ab4039.PNG)
 
 <p> kedua masuk ke login dan isi data sesuai di body & copy hasil access token </p>
 
 ![login](https://user-images.githubusercontent.com/59783691/154883126-f1ae038b-1e73-48d4-9a6c-e02b9dd59249.PNG)
 
 <p> Ketiga masuk ke email, lalu paste kan token tadi di authorization header dan pilih type bearer token seperti digambar</p>
 
 ![mail](https://user-images.githubusercontent.com/59783691/154883129-b79bfe2e-f75a-435b-8b57-52d19f76fd47.PNG)
 
 <p> Cek email yang terkirim </p>
 
 ![mailtrap](https://user-images.githubusercontent.com/59783691/154883735-57e9d21a-cdaf-45d7-ad08-627fa1e9d47e.PNG)
 
 <p> jika sudah selesai silahkan logout </p>
 
 ![logout](https://user-images.githubusercontent.com/59783691/154883128-77a48983-bd49-48a2-a40c-1ba2c11dae3d.PNG)
