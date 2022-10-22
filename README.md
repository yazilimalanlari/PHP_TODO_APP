# PHP Todo App

PHP ile MVC yapısı kurularak geliştirilen bir To Do uygulaması.

## Kullanılan Dil ve Teknolojiler
* PHP
* XML
* HTML5
* CSS3
* JavaScript

## Özellikler
- [x] Routing
    - [x] XML
    - [x] Native
- [x] Database
    - [x] SQLite3
    - [x] MySQL
- [x] MVC
- [x] Views -> Layout System

<a id="database">
    <h2>Veritabanı</h2>
</a>

## Veritabanı ve Tablolar
Veritabanı ve tablolar otomatik olarak oluşturulmaktadır.
1. Eğer bir veritabanı varsa veritabanı yeniden oluşturulmaz.
2. Tablolar da bir değişiklik yapıldığında
   1.  Veritabanını silmek gerekir.
   2.  Veya manuel şekilde bu alanları oluşturmak gerekir.

### Veritabanı Seçimi
SQLite3 ve MySQL desteklenmektedir. 
Hangi veritabanı kullanılmak isteniyorsa .env dosyasındaki ortam değişkeni o şekilde ayarlanmalıdır.

## Routing Sistemi
URL yönlendirmeleri için hem native olarak PHP ile yönlendirme yapılabilir. Hem de XML kullanılarak yapılabilir.
```PHP
// ./kernel/index.php
Config::init(useRoutersXML: false); // true olursa XML yönlendirmeleri aktif olur.
```

## Cache Sistemi
Üretim(production) ortamı için önbellek(cache) sistemi geliştirilmiştir.
Burada amaç performanstır. Şuan için bu sistem sadece yönlendirmeler de çalışıyor.
```ShellScript
php app cache clear # Linux için
```

## Projeyi Ayağa Kaldırma

### Geliştirici Modu (Development)
```ShellScript
php app build # Veritabanını oluşturmak için sadece bir kez çalıştırmak yeterli.
php app dev
```

### Üretim Modu (Production)
**Projenin bu sürümü için önemli notlar**; 
* ***build*** komutu ile veritabanı varsa tekrar oluşturmaz. [Veritabanı](#database) bölümünde bakın.
* ***build*** komutu ile veritabanı daha önce oluşturulmuşsa terminal'de SQL hatası alırsınız.

```ShellScript
php app build
php app start
```