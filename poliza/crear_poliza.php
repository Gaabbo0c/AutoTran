<?php
if (!$res && file_exists("../main.inc.php"))
$res = @include '../main.inc.php';     // to work if your module directory is into dolibarr root htdocs directory
if (!$res && file_exists("../../main.inc.php"))
$res = @include '../../main.inc.php';   // to work if your module directory is into a subdir of root htdocs directory
if (! $res) die("Include of main fails");
require_once('../class/FacturaCompta.class.php');
require_once('../class/EncodingCharset.php');
require_once("../class/xml.class.php");
require_once DOL_DOCUMENT_ROOT.'/autotran/class/poliza.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.product.class.php';
if (file_exists(DOL_DOCUMENT_ROOT.'/contab/class/contabperiodos.class.php')) 
{
	require_once DOL_DOCUMENT_ROOT.'/contab/class/contabperiodos.class.php';
} 
else 
{
	require_once DOL_DOCUMENT_ROOT.'/custom/contab/class/contabperiodos.class.php';
}
include_once DOL_DOCUMENT_ROOT . '/core/actions_linkedfiles.inc.php';

global $db,$conf,$user;

$file = $_POST['xml'];
$uuid = $_POST['uuid'];
$FacturaId = $_POST['IDfact'];

//print_r($uuid." ".$FacturaId);
$archivo = fopen("../Factura/xml/".$uuid . ".xml", "w");
fwrite($archivo, $file);
fclose($archivo);

$datos = preg_split('/ /',$FacturaId, -1);


$sql = "SELECT anio, mes, tipo_pol, cons";
$sql.=" FROM ".MAIN_DB_PREFIX."contab_polizas";
$sql.=" WHERE fk_facture = (SELECT rowid FROM ".MAIN_DB_PREFIX."facture_fourn WHERE ref = '$datos[0]')";
//print_r($sql);
$nm1=0;
$resql=$db->query($sql);
if($resql)
	{
	$nm1 = $db->num_rows($resql);
	$i1 = 0;
	if($nm1)
		{
		while($i1 < $nm1)
			{
			$ojt = $db->fetch_object($resql);
			if($ojt)
			{
			$anio = $ojt->anio;
			$mes = $ojt->mes;
			$tipo = $ojt->tipo_pol;
			$constante = $ojt->cons;		
			}
			$i1++;
			}
		}
	}
	unset($sql);
if($nm1 == 0)
{
	$xml = new xml();
	$leido = $xml->leer($file);

	$filename = $leido["TimbreUUID"][0].".xml";
	$emisorRfc = $leido["EmisorRFC"][0];
	$compSerie = $leido["Serie"][0];
	$compFolio = $leido["Folio"][0];
	$timbreFDFechTimbra = $leido["FechaTimbre"][0];
	$arrConcp = $leido["Conceptos"];
	$arrConTras = $leido["ConceptoTraslado"];
	$arrConRetenIva = $leido["Retencion"];
	$arrConRetenIsr = $leido["RetencionISR"];
	$arrConRetenLocales = $leido["RetencionLocal"];
	$compIppSubTot = $leido["Subtotal"];
	$totimpuesto = $leido["TotalImpuestos"];
	$compImpTot = $leido["Total"];
	$nombreEmisor = $leido["NombreEmisor"];
	$tipoImpuesto = $leido["TipoDeComprobante"];
	
	if($tipoImpuesto != "P")
		{
			if($emisorRfc != $conf->global->MAIN_INFO_SIREN)
			{
			$poliza = new Poliza();
			$año = 0; 
			$mes = 0;
			list($año, $mes, $diacomp) = split('[/.-]', $timbreFDFechTimbra);
			$day=split("[T]",$diacomp);
			$dia = $day[0];
		
			$per = new Contabperiodos($db);
		
			if($año > 0 || $mes > 0)
			{
				if ($per->fetch_by_period($año, $mes)) 
				{
					$periodo_estado = $per->estado;
				}
			}
			else
			{
				$per->fetch_open_period();
				$periodo_estado = $per->estado;
				$año = $per->anio;
				$mes = $per->mes;
			}
			$conceptospoliza = array(
				$arrConcp,
				$arrConTras,
				$arrConRetenIva,
				$arrConRetenIsr,
				$arrConRetenLocales
			);
			
			$sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."facture_fourn ";
			$sql.= "WHERE ref = '$datos[0]'";
			$consulta = $db->query($sql);
			if($consulta)
			{
				$nm = $db->num_rows($consulta);
				$i = 0;
				if($nm)
					{
					while($i < $nm)
						{
						$obt = $db->fetch_object($consulta);
						if($obt)
						{
						$id = $obt->rowid;		
						}
						$i++;
						}
					}
			}
			$respuestapol = $poliza->createpol($año,$mes,$dia,$conf,$db,$conceptospoliza,$uuid,$id,$compImpTot,$nombreEmisor);
			print $respuestapol;
			}
		}
}
else
{

}

?>