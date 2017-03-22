<?php
/*
// Numeros
// =======
//
// Conversiones de numeros PHP
//
// Clase original de Felix de Jesus Carrillo Celerino (dcreate)
// 	https://github.com/dcreate/Numeros/blob/master/letras.php
//
//
// Modificada por Miquel Botanch
//
// Cambios:
//
//  	* He adaptado el código para que muestre dos decimales.
//
//  	* Permite especificar nombres de moneda y de su centésima parte en singular y plural.
//
//  	* Permite de manera opcional substituir un_mil por mil (como en el castellano de España)
//
//  	* Adaptado para que sea directamente compatible con la versión original, por si se actualiza la clase,
//  	  NO SEA NECESARIO corregir las llamadas a lHa función ValorEnLetras().  (ya que ahora tiene 5 parámetros en vez de 2)
//
//  	* Añadida variable $anadir_MN_al_final para añadir M.N. al final de la cadena (en España no se usa este acrónimo)
//
// Modificada por Pedro Oliver
//
//
//	Cambios:
//
//		* Añadida opción para activar o desactivar el uso de idiomas
//		* Añadido una opcion para definir el idioma actual
//		* Añadida opción para definir la ruta de idiomas
//		* Añado funcion llamada l para el uso de idiomas que solo funciona si esta activado el uso de idiomas
//		* Sustituidas todas las cadenas de texto con la funcion $this->l para incorporarlas al sistema de traducciones
//		* Activar o desactivar
*/
class EnLetras
{
  var $Void = "";
  var $SP = " ";
  var $Dot = ".";
  var $Zero = "0";
  var $Neg = "Menos";
  var $substituir_un_mil_por_mil = true;
  var $anadir_MN_al_final = true;
  var $tratar_decimales = true;
  var $activar_idiomas = true;
  var $idioma = "es_ES";
  var $ruta_idiomas = "lang/";
function __construct(){
	// Comprovamos si esta actiado el sistema de idiomas si existe el DirectoryIterator
	if($this->activar_idiomas)
		if(!is_dir($this->ruta_idiomas))
			exit("El directorio \"".$this->ruta_idiomas."\" no existe");
		if(!is_writable($this->ruta_idiomas))
			exit("El directorio \"".$this->ruta_idiomas."\" no tiene permisos necesarios");
}
function ValorEnLetras($x, $Moneda_singular, $Moneda_plural="" ,$Centesima_parte_singular="", $Centesima_parte_plural="")
{
    $s="";
    $Ent="";
    $Frc="";
    $Signo="";

	// para compatibilizar la clase con la version antigua
	$Moneda_plural = ($Moneda_plural == "") ? $Moneda_singular : $Moneda_plural;

    if(floatVal($x) < 0)
     $Signo = $this->Neg . " ";
    else
     $Signo = "";

    if(intval(number_format($x,2,'.','') )!=$x) //<- averiguar si tiene decimales
      $s = number_format($x,2,'.','');
    else
      $s = number_format($x,2,'.','');

    $Pto = strpos($s, $this->Dot);

    if ($Pto === false)
    {
      $Ent = $s;
      $Frc = $this->Void;
    }
    else
    {
      $Ent = substr($s, 0, $Pto );
      $Frc =  substr($s, $Pto+1);
    }

    if($Ent == $this->Zero || $Ent == $this->Void)
       $s = $this->l("Cero")." ";
    elseif( strlen($Ent) > 7)
    {
       $s = $this->SubValLetra(intval( substr($Ent, 0,  strlen($Ent) - 6))) .
            $this->l("Millones") ." ". $this->SubValLetra(intval(substr($Ent,-6, 6)));
    }
    else
    {
      $s = $this->SubValLetra(intval($Ent));
    }

    if (substr($s,-9, 9) == $this->l("Millones")." " || substr($s,-7, 7) == $this->l("Millón")." ")
       $s = $s . $this->l("de")." ";


	if($this->substituir_un_mil_por_mil){
		// En el castellano de España en vez de decir "Un Mil" se dice "Mil"

		if(substr($s,0,6)== $this->l("Un Mil")){
			$s = substr($s,3);
		}

	}

	// para compatibilizar la clase con la version antigua
	if ( !$this->tratar_decimales ){
		// ignora los decimales i los muestra como XX/100

//		$s = $s . $Moneda_singular;
		$s = $s . (intval(abs($x))==1 ? $Moneda_singular : $Moneda_plural);

//	    if($Frc != $this->Void)
		if($Frc != "00")
	    {
	       $s = $s . " " . $Frc. "/100";
	       //$s = $s . " " . $Frc . "/100";
	    }

	}else{
		$s = $s . (intval(abs($x))==1 ? $Moneda_singular : $Moneda_plural);

		if($Frc != "00"){
			$tmpV = new EnLetras();
			$tmpV->anadir_MN_al_final = false;
			$tmpV->tratar_decimales = false;
			$s.= " ".$this->l("con")." ".$tmpV->ValorEnLetras($Frc, $Centesima_parte_singular,$Centesima_parte_plural,"","");
		}
	}

//    $letrass=$Signo . $s;
    return ($Signo . $s ).($this->anadir_MN_al_final?" M.N.":"");

}


function SubValLetra($numero)
{
    $Ptr="";
    $n=0;
    $i=0;
    $x ="";
    $Rtn ="";
    $Tem ="";

    $x = trim("$numero");
    $n = strlen($x);

    $Tem = $this->Void;
    $i = $n;

    while( $i > 0)
    {
       $Tem = $this->Parte(intval(substr($x, $n - $i, 1).
                           str_repeat($this->Zero, $i - 1 )));
       If( $Tem != $this->l("Cero") )
          $Rtn .= $Tem . $this->SP;
       $i = $i - 1;
    }


    //--------------------- GoSub FiltroMil ------------------------------
    $Rtn=str_replace(" ".$this->l("Mil")." ".$this->l("Mil"), " ".$this->l("Un Mil"), $Rtn );
    while(1)
    {
       $Ptr = strpos($Rtn, $this->l("Mil")." ");
       If(!($Ptr===false))
       {
          If(! (strpos($Rtn, $this->l("Mil")." ",$Ptr + 1) === false ))
            $this->ReplaceStringFrom($Rtn, $this->l("Mil")." ", "", $Ptr);
          Else
           break;
       }
       else break;
    }

    //--------------------- GoSub FiltroCiento ------------------------------
    $Ptr = -1;
    do{
       $Ptr = strpos($Rtn, $this->l("Cien")." ", $Ptr+1);
       if(!($Ptr===false))
       {
          $Tem = substr($Rtn, $Ptr + 5 ,1);
          if( $Tem == "M" || $Tem == $this->Void)
             ;
          else
             $this->ReplaceStringFrom($Rtn, $this->l("Cien"), $this->l("Ciento"), $Ptr);
       }
    }while(!($Ptr === false));

    //--------------------- FiltroEspeciales ------------------------------
    $Rtn=str_replace($this->l("Diez Un"), $this->l("Once"), $Rtn );
    $Rtn=str_replace($this->l("Diez Dos"), $this->l("Doce"), $Rtn );
    $Rtn=str_replace($this->l("Diez Tres"), $this->l("Trece"), $Rtn );
    $Rtn=str_replace($this->l("Diez Cuatro"), $this->l("Catorce"), $Rtn );
    $Rtn=str_replace($this->l("Diez Cinco"), $this->l("Quince"), $Rtn );
    $Rtn=str_replace($this->l("Diez Seis"), $this->l("Dieciseis"), $Rtn );
    $Rtn=str_replace($this->l("Diez Siete"), $this->l("Diecisiete"), $Rtn );
    $Rtn=str_replace($this->l("Diez Ocho"), $this->l("Dieciocho"), $Rtn );
    $Rtn=str_replace($this->l("Diez Nueve"), $this->l("Diecinueve"), $Rtn );
    $Rtn=str_replace($this->l("Veinte Un"), $this->l("Veintiun"), $Rtn );
    $Rtn=str_replace($this->l("Veinte Dos"), $this->l("Veintidos"), $Rtn );
    $Rtn=str_replace($this->l("Veinte Tres"), $this->l("Veintitres"), $Rtn );
    $Rtn=str_replace($this->l("Veinte Cuatro"), $this->l("Veinticuatro"), $Rtn );
    $Rtn=str_replace($this->l("Veinte Cinco"), $this->l("Veinticinco"), $Rtn );
    $Rtn=str_replace($this->l("Veinte Seis"), $this->l("Veintiseís"), $Rtn );
    $Rtn=str_replace($this->l("Veinte Siete"), $this->l("Veintisiete"), $Rtn );
    $Rtn=str_replace($this->l("Veinte Ocho"), $this->l("Veintiocho"), $Rtn );
    $Rtn=str_replace($this->l("Veinte Nueve"), $this->l("Veintinueve"), $Rtn );

    //--------------------- FiltroUn ------------------------------
    If(substr($Rtn,0,1) == "M") $Rtn = $this->l("Un")." " . $Rtn;
    //--------------------- Adicionar Y ------------------------------
    for($i=65; $i<=88; $i++)
    {
      If($i != 77)
         $Rtn=str_replace("a " . Chr($i), "*".$this->l(" y ")."" . Chr($i), $Rtn);
    }
    $Rtn=str_replace("*", "a" , $Rtn);
    return($Rtn);
}

function ReplaceStringFrom(&$x, $OldWrd, $NewWrd, $Ptr)
{
  $x = substr($x, 0, $Ptr)  . $NewWrd . substr($x, strlen($OldWrd) + $Ptr);
}


function Parte($x)
{
    $Rtn='';
    $t='';
    $i='';
    Do
    {
      switch($x)
      {
         Case 0:  $t = $this->l("Cero");break;
         Case 1:  $t = $this->l("Un");break;
         Case 2:  $t = $this->l("Dos");break;
         Case 3:  $t = $this->l("Tres");break;
         Case 4:  $t = $this->l("Cuatro");break;
         Case 5:  $t = $this->l("Cinco");break;
         Case 6:  $t = $this->l("Seis");break;
         Case 7:  $t = $this->l("Siete");break;
         Case 8:  $t = $this->l("Ocho");break;
         Case 9:  $t = $this->l("Nueve");break;
         Case 10: $t = $this->l("Diez");break;
         Case 20: $t = $this->l("Veinte");break;
         Case 30: $t = $this->l("Treinta");break;
         Case 40: $t = $this->l("Cuarenta");break;
         Case 50: $t = $this->l("Cincuenta");break;
         Case 60: $t = $this->l("Sesenta");break;
         Case 70: $t = $this->l("Setenta");break;
         Case 80: $t = $this->l("Ochenta");break;
         Case 90: $t = $this->l("Noventa");break;
         Case 100: $t = $this->l("Cien");break;
         Case 200: $t = $this->l("Doscientos");break;
         Case 300: $t = $this->l("Trescientos");break;
         Case 400: $t = $this->l("Cuatrocientos");break;
         Case 500: $t = $this->l("Quinientos");break;
         Case 600: $t = $this->l("Seiscientos");break;
         Case 700: $t = $this->l("Setecientos");break;
         Case 800: $t = $this->l("Ochocientos");break;
         Case 900: $t = $this->l("Novecientos");break;
         Case 1000: $t = $this->l("Mil");break;
         Case 1000000: $t = $this->l("Millón");break;
      }

      If($t == $this->Void)
      {
        $i = $i + 1;
        $x = $x / 1000;
        If($x== 0) $i = 0;
      }
      else
         break;

    }while($i != 0);

    $Rtn = $t;
    Switch($i)
    {
       Case 0: $t = $this->Void;break;
       Case 1: $t = " ".$this->l("Mil");break;
       Case 2: $t = " ".$this->l("Millones");break;
       Case 3: $t = " ".$this->l("Billones");break;
    }
    return($Rtn . $t);
}

/**
 * Control de idiomas function l
 *
 * @param String   	$cadena  	Cadena de texto a traducir
 * @param Array 	$binds 		Array con las variables reemplazables
 *
 *
 * $this->l("Texto a traducir con el valor #numero#", array("#numero#" => 38));
 * Resultado: Texto a traducir con el valor 8
 *
 *
 */

function l($cadena, $binds = array()){
	if($this->activar_idiomas){
		$lang = array();
		if(!file_exists($this->ruta_idiomas.$this->idioma.".php")){
			$fp = fopen($this->ruta_idiomas.$this->idioma.".php", "a+");
			fwrite($fp, '<?php'. PHP_EOL);
			fwrite($fp, PHP_EOL);
			fwrite($fp, '$this->substituir_un_mil_por_mil = false;' . PHP_EOL);
			fwrite($fp, '$this->anadir_MN_al_final = false;' . PHP_EOL);
			fwrite($fp, '$this->tratar_decimales = true;' . PHP_EOL);
			fwrite($fp, PHP_EOL);
			fclose($fp);
			chmod($this->ruta_idiomas.$this->idioma.".php", 0777);
		}

		require($this->ruta_idiomas.$this->idioma.".php");
	    $cadena = str_replace("  ", " ", preg_replace('/\s+/', ' ', $cadena));
	    if(!isset($lang[hash("md5", $cadena)])){
	       	$fp = fopen($this->ruta_idiomas.$this->idioma.".php", "a");
			fwrite($fp, '// Original: '.$cadena . PHP_EOL);
			fwrite($fp, '$lang["'.hash("md5", $cadena).'"] = "'.$cadena.'";'. PHP_EOL);
			fwrite($fp, PHP_EOL);
	        $binded = $cadena;
	        foreach($binds as $key => $value){
	            $binded = str_replace($key, $value, $binded);
	        }
	        $lang[hash("md5", $cadena)] = $binded;
	    } else {
	        $binded = $lang[hash("md5", $cadena)];
	        foreach($binds as $key => $value){
	            $binded = str_replace($key, $value, $binded);
	        }
	    }
	    //return nl2br($binded);
		return $binded;
	}else{
		return $cadena;
	}
}


}

//-------------- Programa principal ------------------------


$total=1234.31;
$V=new EnLetras();

// Se ha introducido en el archivo de idiomas
//$V->substituir_un_mil_por_mil = true;

$V->activar_idiomas = true;

/*
 $con_letra=strtoupper($V->ValorEnLetras($total,"Euros"));
 echo "<b>".$con_letra."</b>";
*/

print $V->ValorEnLetras(1000,"Euro","Euros","Céntimo","Céntimos")."<br>";
print $V->ValorEnLetras(12341234,"Euro","Euros","Céntimo","Céntimos")."<br>";
print $V->ValorEnLetras(123412341234.33,"Euro","Euros","Céntimo","Céntimos")."<br>";
print $V->ValorEnLetras(1234,"Euro","Euros","Céntimo","Céntimos")."<br>";
print $V->ValorEnLetras(1234.01,"Euro","Euros","Céntimo","Céntimos")."<br>";
print $V->ValorEnLetras(1234.11,"Euro","Euros","Céntimo","Céntimos")."<br>";
print $V->ValorEnLetras(1234.21,"Dólar","Dólares","Centavo","Centavos")."<br>";
print $V->ValorEnLetras(-1234.12,"Euro","Euros","Céntimo","Céntimos")."<br>";
print $V->ValorEnLetras(-1234.12,"Euro")."<br>";


?>
