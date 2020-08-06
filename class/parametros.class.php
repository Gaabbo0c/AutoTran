<?php
/* Copyright (C) 2017  Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file        class/parametros.class.php
 * \ingroup     autotran
 * \brief       This file is a CRUD class file for parametros (Create/Read/Update/Delete)
 */

// Put here all includes required by your class file

require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class for parametros
 */
class parametros extends CommonObject
{
	/**
	 * @var string ID to identify managed object
	 */
	public $element = 'parametros';

	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'autotran_parametros';

	/**
	 * @var int  Does parametros support multicompany module ? 0=No test on entity, 1=Test with field entity, 2=Test with link by societe
	 */
	public $ismultientitymanaged = 0;

	/**
	 * @var int  Does parametros support extrafields ? 0=No, 1=Yes
	 */
	public $isextrafieldmanaged = 1;

	/**
	 * @var string String with name of icon for parametros. Must be the part after the 'object_' into object_parametros.png
	 */
	public $picto = 'parametros@autotran';


	/**
	 *  'type' if the field format.
	 *  'label' the translation key.
	 *  'enabled' is a condition when the field must be managed.
	 *  'visible' says if field is visible in list (Examples: 0=Not visible, 1=Visible on list and create/update/view forms, 2=Visible on list only. Using a negative value means field is not shown by default on list but can be selected for viewing)
	 *  'notnull' is set to 1 if not null in database. Set to -1 if we must set data to null if empty ('' or 0).
	 *  'default' is a default value for creation (can still be replaced by the global setup of default values)
	 *  'index' if we want an index in database.
	 *  'foreignkey'=>'tablename.field' if the field is a foreign key (it is recommanded to name the field fk_...).
	 *  'position' is the sort order of field.
	 *  'searchall' is 1 if we want to search in this field when making a search from the quick search button.
	 *  'isameasure' must be set to 1 if you want to have a total on list for this field. Field type must be summable like integer or double(24,8).
	 *  'css' is the CSS style to use on field. For example: 'maxwidth200'
	 *  'help' is a string visible as a tooltip on field
	 *  'comment' is not used. You can store here any text of your choice. It is not used by application.
	 *  'showoncombobox' if value of the field must be visible into the label of the combobox that list record
	 *  'arraykeyval' to set list of value if type is a list of predefined values. For example: array("0"=>"Draft","1"=>"Active","-1"=>"Cancel")
	 */

	// BEGIN MODULEBUILDER PROPERTIES
	/**
	 * @var array  Array with all fields and their property. Do not use it as a static var. It may be modified by constructor.
	 */
	var $lines = array();

	public $fields=array(
		'rowid' => array('type'=>'integer', 'label'=>'ID', 'enabled'=>1, 'visible'=>-1, 'position'=>10, 'notnull'=>1,),
		'organizacionId' => array('type'=>'integer', 'label'=>'OrganizacionId', 'enabled'=>1, 'visible'=>-2, 'position'=>15, 'notnull'=>1,),
		'organizacionNombre' => array('type'=>'varchar(200)', 'label'=>'OrganizacionNombre', 'enabled'=>1, 'visible'=>-2, 'position'=>20, 'notnull'=>1,),
		'organizacionRFC' => array('type'=>'varchar(13)', 'label'=>'OrganizacionRFC', 'enabled'=>1, 'visible'=>-2, 'position'=>25, 'notnull'=>1,),
		'FechaUltimaVerificacion' => array('type'=>'date', 'label'=>'FechaUltimaVerificacion', 'enabled'=>1, 'visible'=>-2, 'position'=>30, 'notnull'=>-1,),
		'FechaInicioDescarga' => array('type'=>'date', 'label'=>'FechaInicioDescarga', 'enabled'=>1, 'visible'=>-2, 'position'=>35, 'notnull'=>1,),
		'satPassword' => array('type'=>'varchar(200)', 'label'=>'SatPassword', 'enabled'=>1, 'visible'=>-2, 'position'=>40, 'notnull'=>1,),
		'tipoDocumento' => array('type'=>'tinyint(4)', 'label'=>'TipoDocumento', 'enabled'=>1, 'visible'=>-2, 'position'=>45, 'notnull'=>1,),
		'MsgError' => array('type'=>'varchar(200)', 'label'=>'MsgError', 'enabled'=>1, 'visible'=>-2, 'position'=>50, 'notnull'=>-1,),
		'Ncer' => array('type'=>'varchar(200)', 'label'=>'Ncer', 'enabled'=>1, 'visible'=>-2, 'position'=>55, 'notnull'=>-1,),
		'cer' => array('type'=>'text', 'label'=>'cer', 'enabled'=>1, 'visible'=>-2, 'position'=>57, 'notnull'=>-1,),
		'Nkey' => array('type'=>'varchar(200)', 'label'=>'Nkey', 'enabled'=>1, 'visible'=>-2, 'position'=>60, 'notnull'=>-1,),
		'archivoKey' => array('type'=>'text', 'label'=>'key', 'enabled'=>1, 'visible'=>-2, 'position'=>63, 'notnull'=>-1,),
		'activo' => array('type'=>'tinyint(1)', 'label'=>'Activo', 'enabled'=>1, 'visible'=>-2, 'position'=>65, 'notnull'=>1,),
		'FechaUltimaActualizacion' => array('type'=>'date', 'label'=>'FechaUltimaActualizacion', 'enabled'=>1, 'visible'=>-2, 'position'=>70, 'notnull'=>-1,),
		'Entity' => array('type'=>'integer', 'label'=>'Entity', 'enabled'=>1, 'visible'=>-2, 'position'=>115, 'notnull'=>1,),
	);
	public $rowid;
	public $organizacionId;
	public $organizacionNombre;
	public $organizacionRFC;
	public $FechaUltimaVerificacion;
	public $FechaInicioDescarga;
	public $satPassword;
	public $tipoDocumento;
	public $MsgError;
	public $Ncer;
	public $cer;
	public $Nkey;
	public $archivoKey;
	public $activo;
	public $FechaUltimaActualizacion;
	public $Entity;
	// END MODULEBUILDER PROPERTIES



	// If this object has a subtable with lines

	/**
	 * @var int    Name of subtable line
	 */
	//public $table_element_line = 'parametrosdet';

	/**
	 * @var int    Field with ID of parent key if this field has a parent
	 */
	//public $fk_element = 'fk_parametros';

	/**
	 * @var int    Name of subtable class that manage subtable lines
	 */
	//public $class_element_line = 'parametrosline';

	/**
	 * @var array  Array of child tables (child tables to delete before deleting a record)
	 */
	//protected $childtables=array('parametrosdet');

	/**
	 * @var parametrosLine[]     Array of subtable lines
	 */
	//public $lines = array();



	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	

	public function __construct(DoliDB $db)
	{
		global $conf, $langs, $user;

		$this->db = $db;

		if (empty($conf->global->MAIN_SHOW_TECHNICAL_ID) && isset($this->fields['rowid'])) $this->fields['rowid']['visible']=0;
		if (empty($conf->multicompany->enabled) && isset($this->fields['entity'])) $this->fields['entity']['enabled']=0;

		// Unset fields that are disabled
		foreach($this->fields as $key => $val)
		{
			if (isset($val['enabled']) && empty($val['enabled']))
			{
				unset($this->fields[$key]);
			}
		}
	}

	/**
	 * Create object into database
	 *
	 * @param  User $user      User that creates
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 * @return int             <0 if KO, Id of created object if OK
	 */
	public function create()
	{
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."autotran_parametros (";
        $sql.= " organizacionNombre";
        $sql.= ", organizacionRFC";
        $sql.= ", FechaInicioDescarga";
        $sql.= ", satPassword";
        $sql.= ", tipoDocumento";
		$sql.= ", Ncer";
		$sql.= ", cer";
		$sql.= ", Nkey";
        $sql.= ", archivoKey";
        $sql.= ", activo";
        $sql.= ", Entity";
        $sql.= ")";
        $sql.= " VALUES (";
        $sql.= "'".$this->organizacionNombre."'";
        $sql.= ", '".$this->organizacionRFC."'";
        $sql.= ", STR_TO_DATE('".$this->FechaInicioDescarga."',\"%d/%m/%Y\")";
        $sql.= ", '".$this->satPassword."'";
        $sql.= ", ".$this->tipoDocumento;
		$sql.= ", '".$this->Ncer."'";
		$sql.= ", '".$this->cer."'";
		$sql.= ", '".$this->Nkey."'";
        $sql.= ", '".$this->key."'";
        $sql.= ", 1";
        $sql.= ", ".$this->Entity;
		$sql.= ")";
		print_r(" ".$sql);
		$resql=$this->db->query($sql);
		if ($resql)
        {
			$this->rowid = $this->db->last_insert_id(MAIN_DB_PREFIX.'autotran_parametros');
		}
	}
	/**
	 * consultaExiste verifica si ya existe registro para el mismo rfc
	 *
	 * @param  $rfc       rfc a verificar
	 * @return bool       respuesta existe o no
	 */
	public function consultaExiste($rfc)
	{
		$existe = false;
		$sql = 'SELECT COUNT(*) as existe ';
		$sql.= 'FROM '.MAIN_DB_PREFIX.'autotran_parametros ';
		$sql.= "WHERE organizacionRFC = '$rfc'";

		$resql=$this->db->query($sql);

		if ($resql)
		{		
			$obj = $this->db->fetch_object($resql);	
			if ($obj)
			{
				$num = $obj->existe;
			}		
		}
		if($num >= 1)
		{
			$existe = true;
		}
		return $existe;


	}

	public function consultaParametros($rfc)
	{
		$sql = "SELECT ";
		$sql.= "Ncer, Nkey, satPassword, FechaInicioDescarga ";
		$sql.= 'FROM '.MAIN_DB_PREFIX.'autotran_parametros ';
		$sql.= "WHERE organizacionRFC = '$rfc'";

		$resql=$this->db->query($sql);

		if ($resql)
		{		
			$obj = $this->db->fetch_object($resql);	
			if ($obj)
			{
				$this->Ncer = $obj->Ncer;
				$this->Nkey = $obj->Nkey;
				$this->satPassword = $obj->satPassword;
				$this->FechaInicioDescarga = $obj->FechaInicioDescarga;
			}		
		}
	}

	public function subirArchivos($file,$rfc,$tipo)
    {
	   $url = 'https://cloud.quantumbit.mx:7005/api/upload?RFC='.$rfc.'&tipo='.$tipo;
	   $fields = $this->bodycer($file,$rfc);
	   $header = 'Content-Type: multipart/form-data;boundary='.$fields[1];
	   
//
	   $resource = curl_init();
	   curl_setopt($resource, CURLOPT_URL, $url);
	   curl_setopt($resource, CURLOPT_HTTPHEADER, $header);
	   curl_setopt($resource, CURLOPT_RETURNTRANSFER, 1);
	   curl_setopt($resource, CURLOPT_POST, 1);
	   curl_setopt($resource, CURLOPT_POSTFIELDS, $fields[0]);
	   return curl_exec($resource);
	   curl_close($resource);

	}
	public function setFechaIni($fecha)
    {
        list($dia, $mes, $año) = split('[/.-]', $fecha);
        return $this->FechaInicioDescarga = $año . '-' . $mes.'/'.$dia ;
	}

	function bodycer($files,$rfc)    {

		// build file parameters
		
		$body[] = implode("\r\n", array(
			"Content-Disposition: form-data; name=\"cer\"",
			"Content-Type: application/pkix-cert",
			"",
			"<@INCLUDE *".$files."*@>",
		));

		
		// generate safe boundary
		do {
			$boundary = "---------------------" . md5(mt_rand() . microtime());
		} while (preg_grep("/{$boundary}/", $body));
		
		// add boundary for each parameters
		array_walk($body, function (&$part) use ($boundary) {
			$part = "--{$boundary}\r\n{$part}";
		});
		
		// add final boundary
		$body[] = "--{$boundary}--";
		$body[] = "";
		
		// set options
		return array(implode("\r\n", $body), $boundary);    
		}

		public function update()
		{
			global $conf, $langs;
			$error=0;
	
			$sqlu = "CALL sp_update_parametros('".$this->organizacionRFC."', '".$this->organizacionNombre."',(SELECT STR_TO_DATE('".$this->FechaInicioDescarga."',\"%d/%m/%Y\")), '$this->satPassword', 1)";
			//return $sqlu;
			$this->db->begin();
			dol_syslog(get_class($this)."::update sql=".$sqlu, LOG_DEBUG);
			$resql = $this->db->query($sqlu);
			if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
			if ($error)
			{
				foreach($this->errors as $errmsg)
				{
					dol_syslog(get_class($this)."::update ".$errmsg, LOG_ERR);
					$this->error.=($this->error?', '.$errmsg:$errmsg);
				}
				$this->db->rollback();
				return $sqlu;
			}
			else
			{
				$this->db->commit();
				return 1;
			}
	
		}
}
