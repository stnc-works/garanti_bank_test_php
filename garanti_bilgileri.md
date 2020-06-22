Güncel kod örnekleri için link;

https://eticaret.garanti.com.tr/mailing/Gvp.zip

 

 

test ortamı raporlama için:

https://sanalposwebtest.garanti.com.tr

 

işyerino           : 7000679

Kullanıcı Adı    : GARANTI

Parola             : destek

Şifre               : 123456WqE

 

 

Provizyon post adresi :

https://sanalposprovtest.garanti.com.tr/VPServlet

 

3d secure sorgulama ve OOS işlemler için post adresi :

https://sanalposprovtest.garanti.com.tr/servlet/gt3dengine

 

 

Kurulum dokümanlarımdaki post adresleri PROD ortama göre düzenlendiğinden test  işlemleriniz  için bu adresleri güncellemeyi unutmayınız.

 

Test mağazası için yazılımda kullanılacak bilgiler:

 

 



 

3D 
MerchantID
7000679
TerminalID
30691297

 

3D_PAY   

MerchantID
7000679
TerminalID
30691298

 

3D_OOS_PAY  

MerchantID
7000679
TerminalID
30691299

 

OOS_PAY 

7000679
TerminalID
30691300

 

3D_FULL   

MerchantID
7000679
TerminalID
30691301

 

 

3D’siz XML kurulumları için yukarıdaki MerchantID ve TerminalID değerlerinden herhangi biri kullanılabilir.

 

MerchantID                                : 7000679

ProvUserID                                : PROVAUT / PROVRFN / PROVOOS

ProvisionPassword                    : 123qweASD/

Securekey                                 : 12345678

  

 

Test sunucusundaki xml işlemlerinde kullanılacak test kredi kartı:

 

Number              4282209027132016

ExpireDate         0520
CVV2                 165

 

Number              4824894728063019

ExpireDate         0723

CVV2                 172

 

 

3D testleri için;

 

Kartlar alacakları MD status ve host cevapları aşağıdaki şekilde;

 

Status :00

MD status 1

 

4282209004348015

Vade 0918 cvv 123

 

375622005485014

Vade 1020 cvv 123

 

 

Test üye işyeri sabit 7000679 dir;

 

asp yazılımdaki örneği;

 

        strMode = "TEST"
        strVersion = "v0.01"
        strTerminalID = "30691297"
        strTerminalID_ = "030691297" 'TerminalID başına 0 ile 9 digit yapılmalı
        strProvUserID = "PROVAUT"
        strProvisionPassword = "123qweASD/" 'SanalPos şifreniz ( PROVAUT kullanıcısının şifresi )
        strUserID = "PROVAUT"
        strMerchantID = "7000679" ‘Uye isyeri no

 