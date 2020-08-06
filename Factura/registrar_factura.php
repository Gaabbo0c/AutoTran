<?php
$res = 0;
if (!$res && file_exists("../main.inc.php"))
$res = @include '../main.inc.php';     // to work if your module directory is into dolibarr root htdocs directory
if (!$res && file_exists("../../main.inc.php"))
$res = @include '../../main.inc.php';   // to work if your module directory is into a subdir of root htdocs directory
if (! $res) die("Include of main fails");
require_once('../class/facturas.class.php');

$total = $_POST['Total'];
$infoFactura = $_POST['Factura'];
$folioF = $_POST['FolioFiscal'];
$id = $_POST['rowId'];

if($infoFactura != 'n')
{
    $datos1 = substr($total,1);
    $datos2 = preg_split('/,/',$datos1);
    $datos = preg_split('/ /',$infoFactura, -1, PREG_SPLIT_OFFSET_CAPTURE);
    unset($total);
    foreach($datos2 as $valor)
    {
        $total = $total . $valor;
    }
    unset($valor);
    $total = floatval($total);
    $TotalFactura = floatval(substr($datos[1][0],0,-6));
    //print_r($total);
    if($TotalFactura != $total)
    {
        print_r($datos2);
    }
    else
    {
        $myobject=new facturas($db);

        $result=$myobject->fetch($id);
        //print $result;
        if ($result < 0) 
        { 
            print "Error"; 
        }
        else 
        {
            print "La linea ".$id." esta cargada\n";
        }
        //print_r($datos);
        $myobject->Factura=$datos[0][0];
        //print_r($myobject);
        $result=$myobject->update($id);
        //print $result;
        if ($result < 0) { $error++; dol_print_error($db,$myobject->error); }
        else 
        print "la linea ".$myobject->id." fue actualizada\n";
        dol_htmloutput_mesg	("Se Ligo la factura: " .$datos[0][0]." con el folio SAT: " . $folioF);

    $db->close();	// Close $db database opened handler
    }
}
else
{
    echo 'Creando nueva factura';
}

?>