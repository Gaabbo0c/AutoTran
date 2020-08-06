<?php
if (!$res && file_exists("../main.inc.php"))
$res = @include '../main.inc.php';     // to work if your module directory is into dolibarr root htdocs directory
if (!$res && file_exists("../../main.inc.php"))
$res = @include '../../main.inc.php';   // to work if your module directory is into a subdir of root htdocs directory
if (! $res) die("Include of main fails");
require_once('../class/facturas.class.php');

$id = $_POST['Id'];
//$script_file = basename(__FILE__);
print $id . "\n";

$myobject=new facturas($db);

// Lectura de la linea

$result=$myobject->fetch($id);
//print $result;
if ($result < 0) 
{ 
    print "Error"; 
}
else 
    print "La linea ".$id." esta cargada\n";


// Actualizacion de linea

$myobject->Factura=" ";
//print_r($myobject);
$result=$myobject->update($id);
//print $result;
if ($result < 0) { $error++; dol_print_error($db,$myobject->error); }
else 
print "la linea ".$myobject->id." fue actualizada\n";

dol_htmloutput_mesg	("Se quito la factura la factura en la linea con ID: " .$id);

$db->close();	// Close $db database opened handler

?>