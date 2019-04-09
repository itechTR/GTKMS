GTKMS
====

[G]üvenli [T]ek [K]ullanımlık [M]esaj [S]istemi

“Bu mesaj 10 saniye içinde kendi kendini imha edecek…” E-postayla veya SMS sistemleri üzerinden mesaj göndermedeki sorun, mesajların sağlayıcı ve operatörlerin saklama politikaları nedeni ile e-posta/SMS unucularında saklanmasıdır. 
Mesajlar gönderen ve alıcı tarafından silinse bile, mesaj sunucuda saklanmaya devam eder.


##Gereksinimler##

* MySQL veritabanı
* PHP5 ve üzeri script çalıştırabilen Web sunucusu
* JavaScript etkinleştirilmiş modern bir web tarayıcısı

## Kurulum ##

* Veritabanını ve tabloyu oluşturmak için MySQL sunucunuzdaki (create PHPMyAdmin) "create_database_tables.sql" komutunu çalıştırın.
* Değişken bildirimlerini "gtkms.php" deki web sunucusunun ana bilgisayar adına işaret edecek şekilde değiştirin. 
* Ayrıca, yeni oluşturduğunuz veritabanı tablosuna erişebilecek veritabanı kullanıcısını ve şifreyi değiştirin.
* "gtkms.php" PHP dosyasını web sunucusuna yerleştirin.

## Kullanım ##

1. Özel bir mesaj oluşturmanız gerekirse, http://domainisminiz/gtkms.php adresine gidin.
2. Mesajı doldurun. ** İpucu ** Herhangi bir tanımlayıcı bilgi istemiyorsanız, imzanızı mesaja eklemeyin. Örneğin, sadece yazın:
   "Selam AlioS öğlen esnaf lokantasında buluşalım. Sana göstermem gereken bir şey var"
3. Mesajı kaydetmeye hazır olduğunuzda, "Mesajı kaydet ve bağlantı oluştur" düğmesini tıkladığınızda sizin için benzersiz bir URL oluşturulur.
4. İstediğiniz mesajlaşma aracına bu URL'yi kopyalayıp yapıştırın. Mesajı alıcıya gönder.
5. Alıcı, URL'yi tarayıcısında açacak ve mesajın görüntülenmesi için mesaj alınacak ve kodu çözülecektir.

** Not ** Mesaj yalnızca bir kez okunabilir. Mesaj aktarım sırasında yakalanırsa, alıcı, ne zaman okunduğunu ve onu görüntülemek için kullanılan bilgisayarın / cihazın IP adresini görecektir. 
Ayrıca, bu yöntemin tamamen kusursuz olmadığını unutmayın. Alıcı bir baskı ekranı veya ekran görüntüsü yakalarsa, mesajın içeriği çevrimdışı kaydedilebilir.
