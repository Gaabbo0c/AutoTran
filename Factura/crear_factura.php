<?php
$res = 0;
if (!$res && file_exists("../main.inc.php"))
$res = @include '../main.inc.php';     // to work if your module directory is into dolibarr root htdocs directory
if (!$res && file_exists("../../main.inc.php"))
$res = @include '../../main.inc.php';   // to work if your module directory is into a subdir of root htdocs directory
if (! $res) die("Include of main fails");

print_r("Creando factura ");
require_once('../class/EncodingCharset.php');
require_once("../class/xml.class.php");

require_once DOL_DOCUMENT_ROOT.'/autotran/class/poliza.class.php';
require_once DOL_DOCUMENT_ROOT.'/autotran/class/polizadet.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.product.class.php';
include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");

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
//print_r("| se agregaron dependencias ");
$file = $_POST['xml'];
//$xmlDoc = new DOMDocument();

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

$archivo = fopen("xml/".$filename, "w");
fwrite($archivo, $file);
fclose($archivo);

//print_r($compImpTot);

//Recibida
//print_r(" | " . $emisorRfc . " - " . $conf->global->MAIN_INFO_SIREN);
if($emisorRfc != $conf->global->MAIN_INFO_SIREN)
{   
        //creamos borrador
        //print_r(" | " . $emisorRfc . " - " . $conf->global->MAIN_INFO_SIREN);
    
    $sql2="SELECT rowid FROM ".MAIN_DB_PREFIX."societe WHERE siren='".$emisorRfc."' AND entity='".$conf->entity."'";
    $dd=$db->query($sql2);
    $rdd=$db->fetch_object($dd);
    $emisorID=$rdd->rowid;
    $object = new FactureFournisseur($db);
    $object->ref= 0;
    $sql="SELECT AUTO_INCREMENT as sig  FROM information_schema.TABLES WHERE TABLE_SCHEMA = '".$dolibarr_main_db_name."' AND TABLE_NAME = '".MAIN_DB_PREFIX."facture_fourn'";
    $rdd=$db->query($sql);
    $rsd=$db->fetch_object($rdd);
    unset($sql);
    if(! empty($compSerie))
    { 
        $object->ref_supplier = $compSerie.'-'.$compFolio;
    }
    else
    { 
        $object->ref_supplier = 'FA'.date('y').date('m').'-'.$compFolio;
    }
    //print_r(" | primer consulta correcta ");
    $sql = 'SELECT ref_supplier';
    $sql .= ' FROM '.MAIN_DB_PREFIX.'facture_fourn';
    $sql .= " WHERE ref_supplier='".$object->ref_supplier."'";
        
    $resql=$db->query($sql);
    if($resql->num_rows >= 1)
    {
        $compFolio = floatval($compFolio) + 1;
        $object->ref_supplier = $compSerie.'-'.$compFolio .'-'.'FA'.date('y').date('m').date('H').date('i').date('s');
        
    }
    //print(" | Segunda consulta correcta " . $object->ref_supplier);
    $object->socid = $emisorID;
    $object->date = $timbreFDFechTimbra;
    $id = $object->create($user);
    //print("| Borrador creado " . $emisorID ." ");
        //print_r("devolvio: " . $id);
        //Borrador creado
        //llenamos info para factura completa
    for($i=0; $i<sizeof($arrConcp); $i++)
    {
        $productsupplier = new ProductFournisseur($db);
        $desc=$arrConcp[$i]['Descripcion'];
        $idprod=0;
        $productsupplier->fourn_pu=floatval($arrConcp[$i]['ValorUnitario']);
        $qty=floatval($arrConcp[$i]['Cantidad']);
        $tvatx=floatval($arrConTras[$i]['TasaOCuota'])*100;
        $fk_product=0;
        $ventil=0;
        $price_base_type='HT';
        if($arrConcp[$i]['Unidad']=='Unidad de servicio'){
            $type=1;
        }
        else
        {
            $type=0;
        }
        $result=$object->addline($desc, $productsupplier->fourn_pu, $tvatx, $localtax1_tx, $localtax2_tx,$qty, $idprod, $remise_percent, '', '', 0, $npr);
        unset($idprod);
        unset($productsupplier);
        unset($tvatx);
        unset($fk_product);
        unset($ventil);
        unset($price_base_type);
        unset($type);
        	
    }
    //print_r("|ProductFournisseur llenado ");
        $sql="SELECT count(*) as exist FROM ".MAIN_DB_PREFIX."const WHERE name='MAIN_MODULE_MULTIDIVISA' AND entity=".$conf->entity;
        $rf=$db->query($sql);
        $rff=$db->fetch_object($rf);
        if($rff->exist>0)
        {
            $sql="INSERT INTO ".MAIN_DB_PREFIX."multidivisa_facture_fourn (tms,fk_object,divisa,entity) VALUES(now(),".$id.",'".GETPOST('cadivisa')."',".$conf->entity.")";
            $re=$db->query($sql);
        }
        $object->fetch($id);
        $object->fetch_thirdparty();
    //print_r("|multidivisa consultado ");
        if($arrConRetenIva!=null)
        {
         $sql = "SHOW COLUMNS FROM ".MAIN_DB_PREFIX."facture_fourn_extrafields LIKE 'reteniva'";
         $resql=$db->query($sql);
         $existe_reteniva = $db->num_rows($resql);
         if( $existe_reteniva > 0 )
            {
                $sql="SELECT rowid FROM ".MAIN_DB_PREFIX."facture_fourn_extrafields WHERE fk_object=".$object->id;
                $rq=$db->query($sql);
                $nr=$db->num_rows($rq);
                if($nr>0)
                {
                    $sql="UPDATE ".MAIN_DB_PREFIX."facture_fourn_extrafields SET reteniva='".$arrConRetenIva[0]["Importe"]."' WHERE fk_object=".$object->id;
                    $rq=$db->query($sql);
                }
                else
                {
                    $sql="INSERT INTO ".MAIN_DB_PREFIX."facture_fourn_extrafields (tms,fk_object,reteniva) VALUES(now(),".$object->id.",'".$arrConRetenIva[0]["Importe"]."')";
                    $rq=$db->query($sql);
                }
            }
        }
    //print_r("|IVA consultado ");
        if($arrConRetenIsr!=null)
        {
            $sql = "SHOW COLUMNS FROM ".MAIN_DB_PREFIX."facture_fourn_extrafields LIKE 'retenisr'";
            $resql=$db->query($sql);
            $existe_retenisr = $db->num_rows($resql);
            if( $existe_retenisr > 0 )
            {
                $sql="SELECT rowid FROM ".MAIN_DB_PREFIX."facture_fourn_extrafields WHERE fk_object=".$object->id;
                $rq=$db->query($sql);
                $nr=$db->num_rows($rq);
                if($nr>0)
                {
                    $sql="UPDATE ".MAIN_DB_PREFIX."facture_fourn_extrafields SET retenisr='".$arrConRetenIsr[0]["Importe"]."' WHERE fk_object=".$object->id;
                    $rq=$db->query($sql);
                }
                else
                {
                    $sql="INSERT INTO ".MAIN_DB_PREFIX."facture_fourn_extrafields (tms,fk_object,retenisr) VALUES(now(),".$object->id.",'".$arrConRetenIsr[0]["Importe"]."')";
                    $rq=$db->query($sql);
                }
            }
        }
    //print_r("|isr consultado ");
        if($arrConRetenLocales!=null)
        {
            $sql = "SHOW COLUMNS FROM ".MAIN_DB_PREFIX."facture_fourn_extrafields LIKE 'retenlocal'";
            $resql=$db->query($sql);
            $existe_retenlocal = $db->num_rows($resql);
            if( $existe_retenlocal > 0 )
            {
                $sql="SELECT rowid FROM ".MAIN_DB_PREFIX."facture_fourn_extrafields WHERE fk_object=".$object->id;
                $rq=$db->query($sql);
                $nr=$db->num_rows($rq);
                if($nr>0)
                {
                    $sql="UPDATE ".MAIN_DB_PREFIX."facture_fourn_extrafields SET retenlocal='".$arrConRetenLocales[0]["Importe"]."' WHERE fk_object=".$object->id;
                    $rq=$db->query($sql);
                }
                else
                {
                    $sql="INSERT INTO ".MAIN_DB_PREFIX."facture_fourn_extrafields (tms,fk_object,retenlocal) VALUES(now(),".$object->id.",'".$arrConRetenLocales[0]["Importe"]."')";
                    $rq=$db->query($sql);
                }
            }
        }
    //print_r("|Impuestos locales consultados ");
        $result = $object->validate($user);
        $upd="UPDATE ".MAIN_DB_PREFIX."facture_fourn SET total_ht=".$compIppSubTot.", total_tva=".$totimpuesto[0]["TotalImpuestosTrasladados"].", total_ttc=".$compImpTot." WHERE rowid=".$id;
        $rqud=$db->query($upd);
    
        $result = $object->generateDocument($object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
    //print(" |Se creo factura ");
        //Ligar factura a autotran
        unset($upd);
        unset($rqud);
        $upd = "UPDATE ".MAIN_DB_PREFIX."autotran_facturas SET Factura = (SELECT ref FROM ".MAIN_DB_PREFIX."facture_fourn WHERE rowid = ".$id.") WHERE FolioFiscal = '".$leido["TimbreUUID"][0]."'";
        $rqud=$db->query($upd);
        //print(" |Se ligo factura ");

        //registra factura en la tabla llx_cfdimx para reportes
        //$xml->registrarXML($id,$db);
        //print(" |registrada para reportes ");

        //crear cabezecera de poliza Diario
        $poliza = new poliza($db);
        $poliza->entity = $conf->entity;
        $poliza->tipo_pol = "D";

        $fecha = explode('-', $timbreFDFechTimbra);

        $poliza->anio = $fecha[0];
        $poliza->mes = $fecha[1];

        $qry = "SELECT cons FROM llx_contab_polizas ";
        $qry.= "WHERE anio = " . $fecha[0]." ";
        $qry.= "AND mes = " . $fecha[1] . " ";
        $qry.= "ORDER BY cons DESC LIMIT 1 ";

        $resql=$db->query($qry);
        if ($resql)
        {
            $obj = $db->fetch_object($resql);
            if ($obj)
            {
                $cons = $obj->cons + 1;
            }
            else
            {
                $cons = 1;
            }
        }
        else
        {
            $cons = 1;
        }
        $poliza->cons = $cons;
        $poliza->fecha = $timbreFDFechTimbra;
        $poliza->concepto = $nombreEmisor;
        $poliza->comentario = "Generado Automaticamente";
        $poliza->fk_facture = $id;
        $poliza->anombrede = "";
        $poliza->numcheque = "";
        $poliza->ant_ctes = 0;
        $poliza->fechahora = $timbreFDFechTimbra;
        $poliza->societe_type = 2;
        $poliza->perajuste = 0;

        //print_r($poliza->crearPoliza());
        //if($poliza->crearPoliza())
        //{
            //print_r($user);
            //$poliza->rowid = $poliza->create($user);
            //print_r(" | Poliza Creada");
            $upd = "UPDATE ".MAIN_DB_PREFIX."autotran_facturas SET Poliza = (SELECT rowId FROM ".MAIN_DB_PREFIX."contab_polizas WHERE rowid = ".$poliza->rowid.") WHERE FolioFiscal = '".$leido["TimbreUUID"][0]."'";
            //$rqud=$db->query($upd);
            //print_r(" | Poliza ligada | ");

          // print_r($poliza->u_campoPoliza($arrConcp,$arrConTras,$emisorRfc,$nombreEmisor,$compImpTot,$user,$leido["TimbreUUID"][0]));

        //}


}
    move_uploaded_file ("xml/".$filename,$conf->fournisseur->facture->dir_output.'/'.get_exdir($object->id,2,0,0,$object,'invoice_supplier').$object->ref."/".$filename);
    //print "<script>window.location='".DOL_MAIN_URL_ROOT."/fourn/facture/card.php?facid=".$id."';</script>";
?>