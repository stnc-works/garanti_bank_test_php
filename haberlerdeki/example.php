<?php
/**
 * Created by PhpStorm.
 * User: bsevgin
 * Date: 15.12.2017
 * Time: 14:50
 */

session_start();
if(!isset($_SESSION['orderNumber']) || !empty($_SESSION['orderNumber'])){
    $_SESSION['orderNumber'] = uniqid();
}

//Sipariş ve ödeme bilgilerini buraya gireceksiniz
$paymentType = "creditcard"; //Kredi kartı için: "creditcard", GarantiPay için: "garantipay"
$params      = [
    'companyName'      => "XX XXX", //Firmanızın adı
    'orderNo'          => $_SESSION['orderNumber'], //Her sipariş için oluşturulan benzersiz sipariş numarası
    'amount'           => "1.20", //Sipariş toplam tutarı, örnek format: 1234 TL için 1234.00 şeklinde girilmelidir
    'installmentCount' => "", //Taksit sayısı, taksit olmayacaksa boş bırakılabilir
    'currencyCode'     => "949", //Ödenecek tutarın döviz cinsinden kodu: TRY=949, USD=840, EUR=978, GBP=826, JPY=392
    'customerIP'       => "127.0.0.1", //Satınalan müşterinin IP adresi
    'customerEmail'    => "tester@gmail.com", //Satınalan müşterinin e-mail adresi
];

//Sadece kredi kartı ile ödeme yapıldığında kart bilgileri alınıyor
if($paymentType=="creditcard"){
    // $params['cardName']         = "XXX XXX"; //(opsiyonel) Kart üzerindeki ad soyad
    // $params['cardNumber']       = "4282209004348015"; //Kart numarası, girilen kart numarası Garanti TEST kartıdır
    // $params['cardExpiredMonth'] = "08"; //Kart geçerlilik tarihi ay
    // $params['cardExpiredYear']  = "22"; //Kart geçerlilik tarihi yıl
    // $params['cardCvv']          = "123"; //Kartın arka yüzündeki son 3 numara(CVV kodu)

    $params['cardName']         = "XXX XXX"; //(opsiyonel) Kart üzerindeki ad soyad
    $params['cardNumber']       = "375622005485014"; //Kart numarası, girilen kart numarası Garanti TEST kartıdır
    $params['cardExpiredMonth'] = "10"; //Kart geçerlilik tarihi ay
    $params['cardExpiredYear']  = "20"; //Kart geçerlilik tarihi yıl
    $params['cardCvv']          = "123"; //Kartın arka yüzündeki son 3 numara(CVV kodu)
}


require_once("GarantiPos.php");
$garantiPos = new GarantiPos($params);

$garantiPos->debugUrlUse                = true; //true/false
$garantiPos->mode                       = "TEST"; //Test ortamı "TEST", gerçek ortam için "PROD"
$garantiPos->terminalMerchantID         = "7000679"; //Üye işyeri numarası
$garantiPos->terminalID                 = "30691298"; //Terminal numarası
$garantiPos->terminalID_                = "0".$garantiPos->terminalID; //Başına 0 eklenerek 9 digite tamamlanmalıdır
$garantiPos->provUserID                 = "PROVAUT"; //Terminal prov kullanıcı adı
$garantiPos->provUserPassword           = "123qweASD/"; //Terminal prov kullanıcı şifresi
$garantiPos->garantiPayProvUserID       = ""; //(GarantiPay kullanılmayacaksa boş bırakılabilir) GarantiPay için prov kullanıcı adı
$garantiPos->garantiPayProvUserPassword = ""; //(GarantiPay kullanılmayacaksa boş bırakabilir) GarantiPay için prov kullanıcı şifresi
$garantiPos->storeKey                   = "676172616e746973616e616c706f74656d64746573727472"; //24byte hex 3D secure anahtarı
$garantiPos->successUrl                 = "http://garanti.test/haberlerdeki/example.php?action=success"; //3D başarıyla sonuçlandığında provizyon çekmek için yönlendirilecek adres
$garantiPos->errorUrl                   = "http://garanti.test/haberlerdeki/example.php?action=error"; //3D başarısız olduğunda yönlenecek sayfa


$action = isset($_GET['action']) ? $_GET['action'] : false;
if($action){
    $garantiPos->debugMode = true;

    $result = $garantiPos->callback($action, $paymentType);
    if($result=="success"){
        echo "başarılı ödeme";
        unset($_SESSION['orderNumber']); //sipariş başarıyla tamamlandığı durumda session siliniyor
    }
    else{
        echo $result['message'];
    }
}
else{
    $garantiPos->debugMode = true;

    $garantiPos->pay($paymentType); //bankaya yönlendirme yapılıyor
}