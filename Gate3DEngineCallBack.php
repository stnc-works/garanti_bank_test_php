
 <?php
function hash_data($result,$responseHashparams,$responseHash)
 
{
$isValidHash = false;
 $storekey="********"; 

 if($responseHashparams!==NULL && $responseHashparams!=="")
 {
 $digestData = "";
 $paramList = explode(":", $responseHashparams);
 
 foreach ($paramList as $param)
 {
 
$value= $result[strtolower($param)];
 
if($value==null)
 {
 $value="";
 }
 
$digestData .= $value;
 }
 
$digestData .= $storekey;
 $hashCalculated = base64_encode(pack('H*',sha1($digestData)));
 
if($responseHash==$hashCalculated)
 {
 $isValidHash = true;
 }


}
 print_r ($isValidHash);
 return $isValidHash;
 }

/*Bu class a g�nderilen veriler de a�a��daki gibi haz�rlan�r ...*/
/*���$responseHashparams = $_POST["hashparams"]; */
/*�� $responseHash = $_POST["hash"]; */
/*�� $result = $_POST; */
/*���$hash_valid = $class_adi-> hash_data( $result,$responseHashparams,$responseHash ); */
/*   $hash_valid de�eri true d�nerse hash de�eri do�rudur, false d�nerse yanl��t�r ... */

