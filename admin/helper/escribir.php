<?php
$res = 0;
if (!$res && file_exists("../../../main.inc.php"))
$res = @include '../../../main.inc.php';   // to work if your module directory is into a subdir of root htdocs directory
if (! $res) die("Include of main fails");
require_once('../../class/parametros.class.php');


$cer = $_POST['filecer'];
$key = $_POST['filekey'];
$pass = $_POST['pass'];
$rpass = $_POST['pass2'];
$fecha = $_POST['Fecha'];
$psw = null;
$parametros=new parametros($db);
$boolc = 0;

$parametros->organizacionNombre = $conf->global->MAIN_INFO_SOCIETE_NOM;
$parametros->organizacionRFC = $conf->global->MAIN_INFO_SIREN;
$parametros->Entity = $conf->entity;

if($cer != "")
{
    $datos = explode('\\',$cer);
    $cer = $datos[2];
    $parametros->Ncer = $cer;
    $boolc++;
}
if($key != "")
{
    $datos = explode('\\',$key);
    $key = $datos[2];
    $parametros->Nkey = $key;
    $boolc++;
}

if($pass == $rpass)
{
    $psw = $pass;
    $parametros->satPassword = $psw;
    $boolc++;
}
else
{
    print_r("Las contraseñas no coinciden");
}

if($fecha != "" || $fecha != null)
{
    $parametros->FechaInicioDescarga = $fecha;
    $boolc++;
}

$parametros->tipoDocumento = 1;

if($parametros->consultaExiste($conf->global->MAIN_INFO_SIREN))
{
    //if($parametros->update() == 1)
    //{
    //    print("Actualizado con exito");
    //}
    //else
    //{
    //    print("Ocurrio un error al intentar actualizar");
    //}
    print $parametros->update();

    require_once('conexionex.php');
    $link = new conexionex();
    $query = "CALL sp_pasar_parametros('".$parametros->organizacionRFC."', '".$parametros->organizacionNombre."',(SELECT STR_TO_DATE('".$parametros->FechaInicioDescarga."',\"%d/%m/%Y\")), '$psw', 1);";
    $link ->ejecutarsp($query);
}
else
{

    if($boolc >= 4)
    {
        $parametros->create();
        require_once('conexionex.php');
        $link = new conexionex();
        $query = "CALL sp_pasar_parametros('".$parametros->organizacionRFC."', '".$parametros->organizacionNombre."',(SELECT STR_TO_DATE('".$parametros->FechaInicioDescarga."',\"%d/%m/%Y\")), '$psw', 1);";
        $link ->ejecutarsp($query);
    }
    else
    {
        print_r("Campos incompetos para creacion");
    }
}
?>