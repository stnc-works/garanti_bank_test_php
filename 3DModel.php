<html>
<head>
    <title></title>
</head>
<body>
    <?php
    $strMode = "TEST";
    $strApiVersion = "v0.01";
    $strTerminalProvUserID = "PROVAUT";
    $strType = "sales";
    $strAmount = "100"; //Ýþlem Tutarý  1.00 TL için 100 gönderilmeli
    $strCurrencyCode = "949";
    $strInstallmentCount = ""; //Taksit Sayýsý. Boþ gönderilirse taksit yapýlmaz
    $strTerminalUserID = "PROVAUT";
    $strOrderID = "6456546";
    $strCustomeripaddress = "127.0.0.1";
    $strcustomeremailaddress = "se@olli.com.tr";
    $strTerminalID = "30691297";
    $strTerminalID_ = "030691297"; //Baþýna 0 eklenerek 9 digite tamamlanmalýdýr.
    $strTerminalMerchantID = "7000679"; //Üye Ýþyeri Numarasý
    $strStoreKey = "12345678"; //3D Secure þifreniz
    $strProvisionPassword = "123qweASD/"; //TerminalProvUserID þifresi
    $strSuccessURL = "http://garanti.test/3DModelResults.php";
    $strErrorURL = "http://garanti.test/3DModelResults.php";
    $SecurityData = strtoupper(sha1($strProvisionPassword.$strTerminalID_));
    $HashData = strtoupper(sha1($strTerminalID.$strOrderID.$strAmount.$strSuccessURL.$strErrorURL.$strType.$strInstallmentCount.$strStoreKey.$SecurityData));
    ?>
 <form action="https://sanalposprovtest.garanti.com.tr/servlet/gt3dengine " method="post">
        3D Security Level: 
        <select name="secure3dsecuritylevel">
            <option value="3D">3D</option>
        </select>
        <br />
        Card Number: <input name="cardnumber" value="4282209004348015" type="text" />
        <br />
        Expire Date (mm): <input name="cardexpiredatemonth" value="09" type="text" />
        <br />
        Expire Date (yy): <input name="cardexpiredateyear" value="18" type="text" />
        <br />
        CVV2: <input name="cardcvv2" type="text" value="123" />
        <br />
        <input id="submit" type="submit" value="��lemi G�nder" />
        <input type="hidden" name="mode" value="<?php  echo $strMode ?>" />
        <input type="hidden" name="apiversion" value="<?php  echo $strApiVersion ?>" />
        <input type="hidden" name="terminalprovuserid" value="<?php  echo $strTerminalProvUserID ?>" />
        <input type="hidden" name="terminaluserid" value="<?php  echo $strTerminalUserID ?>" />
        <input type="hidden" name="terminalmerchantid" value="<?php  echo $strTerminalMerchantID ?>" />
        <input type="hidden" name="txntype" value="<?php  echo $strType ?>" />
        <input type="hidden" name="txnamount" value="<?php  echo $strAmount ?>" />
        <input type="hidden" name="txncurrencycode" value="<?php  echo $strCurrencyCode ?>" />
        <input type="hidden" name="txninstallmentcount" value="<?php  echo $strInstallmentCount ?>" />
        <input type="hidden" name="orderid" value="<?php  echo $strOrderID ?>" />
        <input type="hidden" name="terminalid" value="<?php  echo $strTerminalID ?>" />
        <input type="hidden" name="successurl" value="<?php  echo $strSuccessURL ?>" />
        <input type="hidden" name="errorurl" value="<?php  echo $strErrorURL ?>" />
        <input type="hidden" name="customeremailaddress" value="<?php  echo $strcustomeremailaddress ?>" />
        <input type="hidden" name="customeripaddress" value="<?php  echo $strCustomeripaddress ?>" />
        <input type="hidden" name="secure3dhash" value="<?php  echo $HashData ?>" />
    </form>
</body>
</html>