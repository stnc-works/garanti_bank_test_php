<?php
/**
 * Created by PhpStorm.
 * User: bsevgin
 * Date: 31.10.2017
 * Time: 13:54
 */

class GarantiPos
{
    //Buraya pos bilgileri girilecek(example.php'de buralar set ediliyor) >>>>>
    public $debugMode                  = false;
    public $debugUrlUse                = false;
    public $version                    = "v0.01";
    public $mode                       = "PROD"; //Test ortamı "TEST", gerçek ortam için "PROD"
    public $terminalMerchantID         = ""; //Üye işyeri numarası
    public $terminalID                 = ""; //Terminal numarası
    public $provUserID                 = ""; //Terminal prov kullanıcı adı
    public $provUserPassword           = ""; //Terminal prov kullanıcı şifresi
    public $garantiPayProvUserID       = ""; //GarantiPay için prov kullanıcı adı
    public $garantiPayProvUserPassword = ""; //GarantiPay için prov kullanıcı şifresi
    public $storeKey                   = ""; //24byte hex 3D secure anahtarı
    public $successUrl                 = "?action=success"; //3D başarıyla sonuçlandığında yönlenecek sayfa
    public $errorUrl                   = "?action=error"; //3D başarısız olduğunda yönlenecek sayfa
    //<<<<< Buraya pos bilgileri girilecek

    public $terminalID_;
    public $paymentUrl         = "https://sanalposprov.garanti.com.tr/servlet/gt3dengine";
    public $paymentUrlTest     = "https://sanalposprovtest.garanti.com.tr/servlet/gt3dengine";
    public $debugPaymentUrl    = "https://eticaret.garanti.com.tr/destek/postback.aspx";
    public $provisionUrl       = "https://sanalposprov.garanti.com.tr/VPServlet"; //Provizyon için xml'in post edileceği adres
    public $provisionUrlTest   = "https://sanalposprovtest.garanti.com.tr/VPServlet"; //Provizyon için xml'in post edileceği adres

    public $currencyCode       = "949"; //TRY=949, USD=840, EUR=978, GBP=826, JPY=392
    public $lang               = "tr";
    public $paymentRefreshTime = "0"; //Ödeme alındıktan bekletilecek süre
    public $timeOutPeriod                    = "60";
    public $addCampaignInstallment           = "N";
    public $totalInstallamentCount           = "0";
    public $installmentOnlyForCommercialCard = "N";

    //GarantiPay tanımlamalar
    public $useGarantipay    = "Y"; //GarantiPay kullanımı: Y/N
    public $useBnsuseflag    = "Y"; //Bonus kullanımı: Y/N
    public $useFbbuseflag    = "Y"; //Fbb kullanımı: Y/N
    public $useChequeuseflag = "N"; //Çek kullanımı: Y/N
    public $useMileuseflag   = "N"; //Mile kullanımı: Y/N

    public $companyName;
    public $orderNo;
    public $amount;
    public $installmentCount;
    public $cardName;
    public $cardNumber;
    public $cardExpiredMonth;
    public $cardExpiredYear;
    public $cardCVV;
    public $customerIP;
    public $customerEmail;
    public $orderAddress;

    //Bankadan dönen hata kodları ve mesajları
    public $mdStatuses = [
        0 => "Doğrulama başarısız, 3-D Secure imzası geçersiz",
        1 => "Tam doğrulama",
        2 => "Kart sahibi banka veya kart 3D-Secure üyesi değil",
        3 => "Kartın bankası 3D-Secure üyesi değil",
        4 => "Kart sahibi banka sisteme daha sonra kayıt olmayı seçmiş",
        5 => "Doğrulama yapılamıyor",
        7 => "Sistem hatası",
        8 => "Bilinmeyen kart numarası",
        9 => "Üye işyeri 3D-Secure üyesi değil",
    ];

    /**
     * Ödeme işlemleri için gerekli sipariş ve ödeme bilgileri setleniyor
     *
     * @param $params
     */
    public function __construct($params)
    {
        $this->companyName      = $params['companyName'];
        $this->orderNo          = $params['orderNo']; //Her işlemde yeni sipariş numarası gönderilmeli
        $this->amount           = str_replace([",","."], "", $params['amount']); //İşlem Tutarı 1 TL için 1.00 gönderilmeli
        $this->installmentCount = $params['installmentCount']>1 ? $params['installmentCount'] : ""; //Taksit yapılmayacaksa boş gönderilmeli
        $this->currencyCode     = $params['currencyCode'] ? $params['currencyCode'] : $this->currencyCode;
        $this->customerIP       = $params['customerIP'];
        $this->customerEmail    = $params['customerEmail'];
        $this->cardName         = $params['cardName'];
        $this->cardNumber       = $params['cardNumber'];
        $this->cardExpiredMonth = $params['cardExpiredMonth'];
        $this->cardExpiredYear  = $params['cardExpiredYear'];
        $this->cardCVV          = $params['cardCvv'];

        //Fatura bilgileri gönderildiğinde ekleniyor
        if(!empty($params['orderAddresses'])){
            $this->orderAddresses = $params['orderAddresses'];
        }
    }

    /**
     * Kredi kartı ile ödeme için buraya istek yapılacak
     */
    public function pay($type="creditcard")
    {
        if($type=="creditcard"){
            $params = [
                "secure3dsecuritylevel" => "3D",
                "txntype"               => "sales",
                "cardname"              => $this->cardName,
                "cardnumber"            => $this->cardNumber,
                "cardexpiredatemonth"   => $this->cardExpiredMonth,
                "cardexpiredateyear"    => $this->cardExpiredYear,
                "cardcvv2"              => $this->cardCVV,
                "refreshtime"           => $this->paymentRefreshTime,
            ];
        }
        elseif($type=="garantipay"){
            $this->provUserID       = $this->garantiPayProvUserID;
            $this->provUserPassword = $this->garantiPayProvUserPassword;
            $params                 = [
                "secure3dsecuritylevel" => "CUSTOM_PAY",
                "txntype"               => "gpdatarequest",
                "txnsubtype"            => "sales",
                "garantipay"            => $this->useGarantipay,
                "bnsuseflag"            => $this->useBnsuseflag,
                "fbbuseflag"            => $this->useFbbuseflag,
                "chequeuseflag"         => $this->useChequeuseflag,
                "mileuseflag"           => $this->useMileuseflag,
                "refreshtime"           => $this->paymentRefreshTime,
            ];
        }

        $this->redirect_for_payment($params);
    }

    /**
     * Bankadan dönen cevap success ise burası çağrılacak
     *
     * @param string $type
     *
     * @return bool|mixed
     */
    public function callback($action="", $type="creditcard")
    {
        if($type=="creditcard"){
            return $this->creditcard_callback($action);
        }
        elseif($type=="garantipay"){
            return $this->garantipay_callback();
        }
    }

    /**
     * Kredi kartı ile ödemede success durumunda burası çağrılacak
     *
     * @return bool|mixed
     */
    private function creditcard_callback($action="")
    {
        $postParams = $_POST;

        if($this->debugMode){
            echo '<pre>'.var_export($postParams, true).'</pre>';
        }

        $strMDStatus = isset($this->mdStatuses[$postParams["mdstatus"]]) ? $postParams["mdstatus"] : 7;
        if(!in_array($strMDStatus, [1,2,3,4])){
            if($postParams['errmsg']){
                $result = $postParams['errmsg'];
            }
            else{
                $result = $this->mdStatuses[$strMDStatus];
            }
        }
        else{
            $result = false;
        }

        if($action=="success" && !$result){
            //Tam Doğrulama, Kart Sahibi veya bankası sisteme kayıtlı değil, Kartın bankası sisteme kayıtlı değil, Doğrulama denemesi, kart sahibi sisteme daha sonra kayıt olmayı seçmiş responselarını alan işlemler için Provizyon almaya çalışıyoruz
            if(in_array($strMDStatus, [1,2,3,4])){
                $strNumber                = ""; //Kart bilgilerinin boş gitmesi gerekiyor
                $strExpireDate            = ""; //Kart bilgilerinin boş gitmesi gerekiyor
                $strCVV2                  = ""; //Kart bilgilerinin boş gitmesi gerekiyor
                $strCardholderPresentCode = "13"; //3D Model işlemde bu değer 13 olmalı
                $strType                  = $postParams["txntype"];
                $strMotoInd               = "N";
                $strAuthenticationCode    = $postParams["cavv"];
                $strSecurityLevel         = $postParams["eci"];
                $strTxnID                 = $postParams["xid"];
                $strMD                    = $postParams["md"];
                $SecurityData             = strtoupper(sha1($this->provUserPassword.$this->terminalID_));
                $HashData                 = strtoupper(sha1($this->orderNo.$this->terminalID.$this->amount.$SecurityData)); //Daha kısıtlı bilgileri HASH ediyoruz.

                //Provizyona Post edilecek XML Şablonu
                $strXML = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                <GVPSRequest>
                    <Mode>{$this->mode}</Mode>
                    <Version>{$this->version}</Version>
                    <ChannelCode></ChannelCode>
                    <Terminal>
                        <ProvUserID>{$this->provUserID}</ProvUserID>
                        <HashData>{$HashData}</HashData>
                        <UserID>{$this->terminalMerchantID}</UserID>
                        <ID>{$this->terminalID}</ID>
                        <MerchantID>{$this->terminalMerchantID}</MerchantID>
                    </Terminal>
                    <Customer>
                        <IPAddress>{$this->customerIP}</IPAddress>
                        <EmailAddress>{$this->customerEmail}</EmailAddress>
                    </Customer>
                    <Card>
                        <Number>{$strNumber}</Number>
                        <ExpireDate>{$strExpireDate}</ExpireDate>
                        <CVV2>{$strCVV2}</CVV2>
                    </Card>
                    <Order>
                        <OrderID>{$this->orderNo}</OrderID>
                        <GroupID></GroupID>
                        <AddressList>
                            <Address>
                                <Type>B</Type>
                                <Name></Name>
                                <LastName></LastName>
                                <Company></Company>
                                <Text></Text>
                                <District></District>
                                <City></City>
                                <PostalCode></PostalCode>
                                <Country></Country>
                                <PhoneNumber></PhoneNumber>
                            </Address>
                        </AddressList>
                    </Order>
                    <Transaction>
                        <Type>{$strType}</Type>
                        <InstallmentCnt>{$this->installmentCount}</InstallmentCnt>
                        <Amount>{$this->amount}</Amount>
                        <CurrencyCode>{$this->currencyCode}</CurrencyCode>
                        <CardholderPresentCode>{$strCardholderPresentCode}</CardholderPresentCode>
                        <MotoInd>{$strMotoInd}</MotoInd>
                        <Secure3D>
                            <AuthenticationCode>{$strAuthenticationCode}</AuthenticationCode>
                            <SecurityLevel>{$strSecurityLevel}</SecurityLevel>
                            <TxnID>{$strTxnID}</TxnID>
                            <Md>{$strMD}</Md>
                        </Secure3D>
                    </Transaction>
                </GVPSRequest>";



                $provisionUrl=$this->debugUrlUse ? $this->provisionUrlTest: $this->provisionUrl;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $this->provisionUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "data=".$strXML);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                $resultContent = curl_exec($ch);
                curl_close($ch);

                if($this->debugMode){
                    echo '<pre>'.var_export($resultContent, true).'</pre>';
                }

                $resultXML       = simplexml_load_string($resultContent);
                $responseCode    = $resultXML->Transaction->Response->Code;
                $responseMessage = $resultXML->Transaction->Response->Message;
                if($responseCode=="00" || $responseMessage=="Approved"){
                    $result = true; //Ödeme başarıyla alındı
                }
                else{
                    $result = $resultXML->Transaction->Response->ErrorMsg[0]; //Hata mesajı gönderiliyor
                }
            }
        }

        return $this->result($result);
    }

    /**
     * GarantiPAY ile ödemede success durumunda burası çağrılacak
     *
     * @return bool
     */
    private function garantipay_callback()
    {
        $postParams = $_POST;

        if($this->debugMode){
            echo '<pre>'.var_export($postParams, true).'</pre>';
        }

        //GarantiPay için dönen cevabın bankadan geldiği doğrulanıyor
        $responseHashparams = $postParams["hashparams"];
        $responseHash       = $postParams["hash"];
        $isValidHash        = false;
        if($responseHashparams!==null && $responseHashparams!==""){
            $digestData = "";
            $paramList  = explode(":", $responseHashparams);
            foreach($paramList as $param){
                if(isset($postParams[strtolower($param)])){
                    $value = $postParams[strtolower($param)];
                    if($value==null){
                        $value = "";
                    }
                    $digestData .= $value;
                }
            }

            $digestData     .= $this->storeKey;
            $hashCalculated = base64_encode(pack('H*', sha1($digestData)));

            if($responseHash==$hashCalculated){
                $isValidHash = true;
            }
        }

        if($isValidHash){
            $result = true; //Ödeme başarıyla alındı
        }
        else{
            $result = $postParams['errmsg']; //Hata mesajı gönderiliyor
        }

        return $this->result($result);
    }

    /**
     * Ödeme için banka ekranına yönlendirme işlemi yapılıyor
     *
     * @param $params
     */
    private function redirect_for_payment($params)
    {
        $params['companyname']                      = $this->companyName;
        $params['apiversion']                       = $this->version;
        $params['mode']                             = $this->mode;
        $params['terminalprovuserid']               = $this->provUserID;
        $params['terminaluserid']                   = $this->terminalID;
        $params['terminalid']                       = $this->terminalID;
        $params['terminalmerchantid']               = $this->terminalMerchantID;
        $params['orderid']                          = $this->orderNo;
        $params['customeremailaddress']             = $this->customerEmail;
        $params['customeripaddress']                = $this->customerIP;
        $params['txnamount']                        = $this->amount;
        $params['txncurrencycode']                  = $this->currencyCode;
        $params['txninstallmentcount']              = $this->installmentCount;
        $params['successurl']                       = $this->successUrl;
        $params['errorurl']                         = $this->errorUrl;
        $params['lang']                             = $this->lang;
        $params['txntimestamp']                     = time();
        $params['txntimeoutperiod']                 = $this->timeOutPeriod;
        $params['addcampaigninstallment']           = $this->addCampaignInstallment;
        $params['totallinstallmentcount']           = $this->totalInstallamentCount;
        $params['installmentonlyforcommercialcard'] = $this->installmentOnlyForCommercialCard;

        $SecurityData           = strtoupper(sha1($this->provUserPassword.$this->terminalID_));
        $HashData               = strtoupper(sha1($this->terminalID.$params['orderid'].$params['txnamount'].$params['successurl'].$params['errorurl'].$params['txntype'].$params['txninstallmentcount'].$this->storeKey.$SecurityData));
        $params['secure3dhash'] = $HashData;

        if($this->debugMode){
            echo "<pre>";
            print_r($params);
            echo "<pre>";
        }

        print('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">');
        print('<html>');
        print('<body>');
        print('<form action="'.($this->debugUrlUse ? $this->paymentUrlTest : $this->paymentUrl).'" method="post" id="three_d_form"/>');
        foreach($params as $name => $value){
            print('<input type="hidden" name="'.$name.'" value="'.$value.'"/>');
        }
        if($this->orderAddress){
            $i = 1;
            foreach($this->orderAddress as $orderAdress){
                print('<input type="hidden" name="orderaddresscount" value="'.$i.'"/>');
                foreach($orderAdress as $name => $value){
                    print('<input type="hidden" name="'.$name.$i.'" value="'.$value.'"/>');
                }
                $i++;
            }
        }
        print('<input type="submit" value="Öde" style="'.($this->debugMode ? '' : 'display:none;').'"/>');
        print('<noscript>');
        print('<br/>');
        print('<div style="text-align:center;">');
        print('<h1>3D Secure Yönlendirme İşlemi</h1>');
        print('<h2>Javascript internet tarayıcınızda kapatılmış veya desteklenmiyor.<br/></h2>');
        print('<h3>Lütfen banka 3D Secure sayfasına yönlenmek için tıklayınız.</h3>');
        print('<input type="submit" value="3D Secure Sayfasına Yönlen">');
        print('</div>');
        print('</noscript>');
        print('</form>');
        print('</body>');
        if(!$this->debugMode){
            print('<script>document.getElementById("three_d_form").submit();</script>');
        }
        print('</html>');
        exit();
    }

    /**
     * Ödeme sonrası sonuç döndürülüyor
     */
    private function result($result=false)
    {
        $r = [];
        if($result===true){
            $r['status']  = "success";
            $r['message'] = "OK";
        }
        else{
            $r['status']  = "error";
            $r['message'] = $result;
        }

        return $r;
    }

}