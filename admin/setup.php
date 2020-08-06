<?php
/* 
 * Copyright (C) 2018	   Gabriel R. Carpio Meneses <gabriel@quantumbit.mx>
 */

/**
 * \file    htdocs/autoTran/admin/setup.php
 * \ingroup autoTran
 * \brief   autoTran setup page.
 */

// Cargar Dolibarr environment


include("../../main.inc.php");


global $langs, $user,$conf,$db;

// Libraries
require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";
require_once DOL_DOCUMENT_ROOT . "/core/lib/files.lib.php";
require_once DOL_DOCUMENT_ROOT . "/core/class/html.formfile.class.php";
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once '../lib/autotran.lib.php';
include DOL_DOCUMENT_ROOT . "/core/actions_fetchobject.inc.php";
require_once ('../class/parametros.class.php');
// Translations
$langs->loadLangs(array("admin", "autoTran@autoTran"));

// Access control
if (! $user->admin) accessforbidden();

// Parameters
$action = GETPOST('action', 'alpha');
$backtopage = GETPOST('backtopage', 'alpha');

$arrayofparameters=array(
	'Certificado FIEL(.cer)'=>array('css'=>'minwidth200','enabled'=>1),
	'Llave FIEL(.key)' =>array('css'=>'minwidth500','enabled'=>1)
);

$psw = 'Contraseña FIEL';
$psw2 = 'Repetir contraseña';
$fi = 'Fecha Inicial de Descarga';
$ent = $conf->entity; //Numero de organizacion
$form1 = new Form($db);
$creacion = false;
$txtCer;
$txtKey;
$indicador = 0;
/*
 * Actions
 */

//Verificar registros
$parametros = new parametros($db);

if($parametros->consultaExiste($conf->global->MAIN_INFO_SIREN))
{
	$parametros->consultaParametros($conf->global->MAIN_INFO_SIREN);

	$txtCer = $parametros->Ncer;
	$txtKey = $parametros->Nkey;
	$psw0 = $parametros->satPassword;
	$Fid = $parametros->FechaInicioDescarga;
}
else
{
	$creacion = true;
}

/*
 * View
 */

$page_name = "Configuración AutoTran";
llxHeader('', $langs->trans($page_name));

// Subheader
$linkback = '<a href="'.($backtopage?$backtopage:DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1').'">'.$langs->trans("BackToModuleList").'</a>';
print load_fiche_titre($langs->trans($page_name), $linkback, 'object_autoTran@autoTran');

// Configuration header
$head = autoTranAdminPrepareHead();
dol_fiche_head($head, 'settings', '', -1, "autoTran@autoTran");


// Setup page goes here
echo $langs->trans("Configuración");

	//action="file.php"
	print "<form enctype='multipart/form-data' method='POST'>";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'"><script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="../js/ajax.js"></script>';
	print '<input type="hidden" name="action">';

	//Inicio Tabla
	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre"><td class="titlefield">'.$langs->trans("Name").'</td><td>'.$langs->trans("Value").'</td></tr>';

	//Carga de archivos
	$n = 0;
	foreach($arrayofparameters as $key => $val)
	{
		print '<tr class="oddeven">';
		print '<td>';
		print $form->textwithpicto($langs->trans($key),$langs->trans($key.'Tooltip'));
		print '</td><td><input type="hidden" name="MAX_FILE_SIZE" value="30000" />';
		if($n == 0)
		{
			print '<input type="file" name="fileCer" accept=".cer" id="fileCer"></input>';
			print '<label for="fileCer"> <br>'.$txtCer.'</label>';		
		}
		else
		{
			print '<input type="file" name="fileKey" accept=".key" id="fileKey"></input>';
			print '<label for="fileKey"><br>'.$txtKey.'</label>';
		}
		$n = $n+1;	
		print '</td></tr>';
	}

	//Contraseña FIEL
	print '<tr class="oddeven">';
	print '<td>';
	print $form->textwithpicto($langs->trans($psw),$langs->trans($psw.'Tooltip'));
	print '</td><td>';
	if($psw0 != null || $psw0 != '')
	{
		print '<input id="privateKeyPassword" type="password" name="privateKeyPassword"  placeholder="Contraseña" value="'.$psw0.'">';
	}
	else
	{
		print '<input id="privateKeyPassword" type="password" name="privateKeyPassword"    placeholder="Contraseña" value="'.$conf->global->$psw.'">';
	}
	print '</td></tr>';
	print '<tr class="oddeven">';
	print '<td>';
	print $form->textwithpicto($langs->trans($psw2),$langs->trans($psw2.'Tooltip'));
	print '</td><td>';
	if($psw0 != null || $psw0 != '')
	{
		print '<input id="privateKeyPassword2" type="password" name="privateKeyPassword2" placeholder="Contraseña" value="'.$psw0.'">';
	}
	else
	{
		print '<input id="privateKeyPassword2" type="password" name="privateKeyPassword2"  placeholder="Contraseña" value="'.$conf->global->$psw2.'">';
	}
	print '</td></tr>';
	//Fecha Inicial 
	print '<tr class="oddeven">';
	print '<td>';
	print $form->textwithpicto($langs->trans($fi),$langs->trans($fi.'Tooltip'));
	print '</td><td>';
	$form1->select_date($Fid, 'fecha', 0, 0, 0, 'FDI');
	print '</td></tr>';
	print '</table>';
	//Boton Modificar
	print '<br><div class="right">';
	print '<input class="button" type="submit" id="btnUpload" value="'.$langs->trans("Modify").'" onClick="Up( $(\'#fileCer\')[0].files[0],\''.$conf->global->MAIN_INFO_SIREN.'\',$(\'#fileKey\')[0].files[0],document.getElementById(\'fileCer\').value,document.getElementById(\'fileKey\').value,document.getElementById(\'privateKeyPassword\').value,document.getElementById(\'privateKeyPassword2\').value,document.getElementById(\'fecha\').value)">';
	print '</div>';
	print '</form>';
	print '<br>';

	if($action == 'update')
	{
		
	}

// Page end
dol_fiche_end();

llxFooter();
$db->close();