<?php
/**
 * Created by Sinan Taga.
 * User: sinan
 * Date: 31/05/14
 * Time: 19:47
 */

require_once __DIR__ . '/vendor/autoload.php';

/* EST Sanal Pos Örneği
 * Test Bilgileri ile test sunucusuna gönderildi.
 */
/*
$est = new \SanalPos\Est\SanalPosEst('isbank', '700100000', 'ISBANKAPI', 'ISBANK07');
$est->setCard('4508034508034509', '12', '16', '000');

// 125,25 liralık bir sipariş. 3 Taksit
$est->setOrder('deneme1234', 'test@test.com', '125,25', '3');
$est->setMode('TEST');
$result = new \SanalPos\Est\SanalPosResponseEst($est->pay());

// Diğer metodlar
// PreAuth
$result = new \SanalPos\Est\SanalPosResponseEst($est->pay(1));

// PostAuth
$result = new \SanalPos\Est\SanalPosResponseEst($est->postAuth('deneme1234'));

// Siparişi iptal et
$result = new \SanalPos\Est\SanalPosResponseEst($est->cancel('deneme1234'));

// Siparişin 25 lirasını iade et
$result = new \SanalPos\Est\SanalPosResponseEst($est->refund('deneme1234', '25'));

var_dump($result);
*/
/*
 * Garanti Sanal Pos Örneği
 * Test Bilgileri ile test sunucuna gönderiliyor
 */
$pos = new \SanalPos\Garanti\SanalPosGaranti('7000679', '30691297', 'PROVAUT', '123qweASD/', 'PROVAUT');
$pos->setCard('375622005485014', '10', '20', '123');

// 100 liralık peşin sipariş var. 
$pos->setOrder('amrk12345', 'tesfdft@testd.com', '100');
$pos->setMode('TEST');
print_r($pos->pay());