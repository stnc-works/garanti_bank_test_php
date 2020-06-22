<?php
/**
 * Created by PhpStorm.
 * User: bsevgin
 * Date: 15.12.2017
 * Time: 14:50
 */

session_start();
if (!isset($_SESSION['orderNumber']) || !empty($_SESSION['orderNumber'])) {
    $_SESSION['orderNumber'] = uniqid();
}
// $SecurityData = strtoupper(sha1($this->password . str_pad($this->terminalId, 9, '0', STR_PAD_LEFT)));
// $HashData = strtoupper(sha1($this->order['orderId'] . $this->terminalId . $this->card['number'] . $this->order['total'] . $SecurityData));
// Pos tanımları, sipariş bilgileri ve ödeme bilgileri burada tanımlanıyor
$params = array(
    // Pos tanımları (Pos panelinde tanımlanıp buraya girilecek)
    'mode' => "TEST", // Pos modu, test için: "TEST", production için: "PROD"
    'merchantID' => "7000679", // Merchant numarası
    'terminalID' => "30691298", // Terminal numarası
    'provUserID' => "PROVAUT", // Provision kullanıcı adı
    'provUserPassword' => "123qweASD/", // Provision kullanıcı parolası


    'garantiPayProvUserID' => "PROVOOS", // GarantiPay için provision kullanıcı adı
    'garantiPayProvUserPassword' => "XXXXX", // GarantiPay için provision kullanıcı parolası

    // garantisanalpotemdtesrtr
    // 'storeKey' => "676172616e746973616e616c706f74656d64746573727472", // 24byte hex 3D secure anahtarı
    // 'storeKey' => "garantisanalpotemdtesrtr", // 24byte hex 3D secure anahtarı
    'storeKey' => "12345678", // 24byte hex 3D secure anahtarı
    'successUrl' => "http://garanti.test/example_gateway.php?action=success", // Başarılı ödeme sonrası dönülecek adres
    'errorUrl' => "http://garanti.test/example_gateway.php?action=error", // Hatalı ödeme sonrası dönülecek adres
    'companyName' => "GarantiPos PHP", // Firma adı
    'paymentType' => "creditcard", // Ödeme tipi - kredi kartı için: "creditcard", GarantiPay için: "garantipay"


    // Müşteri tanımları
    'orderNo' => $_SESSION['orderNumber'], // Sipariş numarası
    'amount' => 120, // Çekilecek tutar (ondalıklı olarak değil tam sayı olarak gönderilmeli, örn. 1.20tl için 120 gönderilmeli)
    'installmentCount' => "", // Tek çekim olacaksa boş bırakılmalıdır
    'currencyCode' => 949, // Döviz cinsi kodu(varsayılan:949): TRY=949, USD=840, EUR=978, GBP=826, JPY=392
    'customerIP' => $_SERVER['REMOTE_ADDR'], // Müşteri IP adresi
    'customerEmail' => "emma_adresim@gmail.com", // Müşteri e-mail adresi

    // Kart bilgisi tanımları (GarantiPay ile ödemede bu alanların doldurulması zorunlu değildir)
    'cardName' => "sdsd sdsd", // Kart üzerindeki ad soyad
    'cardNumber' => "4282209004348015", // Kart numarası (16 haneli boşluksuz)
    'cardExpiredMonth' => "09", // Kart geçerlilik tarihi ay
    'cardExpiredYear' => "18", // Kart geçerlilik tarihi yıl (yılın son 2 hanesi)
    'cardCvv' => "123", // Kartın arka yüzündeki son 3 numara(CVV kodu)
);

print_r($params);
// GarantiPos sınıfı tanımlanıyor
require_once("GarantiPos.php");
$garantipos = new GarantiPos();
$garantipos->debugMode = true;
$params['paymentType'] = isset($_POST['paymenttype']) ? $_POST['paymenttype'] : $params['paymentType'];
$garantipos->setParams($params);

$action = isset($_GET['action']) ? $_GET['action'] : false;
if ($action) {

    $result = $garantipos->callback($action);
    if ($result['success'] == 'success') {
        unset($_SESSION['orderNumber']); // Sipariş başarıyla tamamlandığı için session siliniyor
    }

    print_r($result);
} else {
    $garantipos->debugUrlUse = false; // Parametre değerlerinin check edildiği adrese gönderilmesi

    $garantipos->pay(); // 3D doğrulama için bankaya yönlendiriliyor
}
