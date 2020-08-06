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
 * \file        class/poliza.class.php
 * \ingroup     descargasat
 * \brief       This file is a CRUD class file for poliza (Create/Read/Update/Delete)
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';
require_once "polizadet.class.php";
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabpolizasdet.class.php';

/**
 * Class for poliza
 */
class poliza extends CommonObject
{
	/**
	 * @var string ID to identify managed object
	 */
	public $element = 'poliza';

	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'contab_polizas';

	/**
	 * @var int  Does poliza support multicompany module ? 0=No test on entity, 1=Test with field entity, 2=Test with link by societe
	 */
	public $ismultientitymanaged = 0;

	/**
	 * @var int  Does poliza support extrafields ? 0=No, 1=Yes
	 */
	public $isextrafieldmanaged = 1;

	/**
	 * @var string String with name of icon for poliza. Must be the part after the 'object_' into object_poliza.png
	 */
	public $picto = 'poliza@autotran';

	// BEGIN MODULEBUILDER PROPERTIES
	/**
	 * @var array  Array with all fields and their property. Do not use it as a static var. It may be modified by constructor.
	 */
	public $fields=array( 
'rowid' =>array('type'=>'integer', 'label'=>'ID', 'enabled'=>1, 'visible'=>-2, 'notnull'=>1, 'position'=>10), 
'entity' =>array('type'=>'integer', 'label'=>'Entity', 'enabled'=>1, 'visible'=>-2, 'notnull'=>1, 'position'=>15), 
'tipo_pol' =>array('type'=>'varchar(1)', 'label'=>'Tipo pol', 'enabled'=>1, 'visible'=>-2, 'notnull'=>1, 'position'=>20), 
'cons' =>array('type'=>'integer', 'label'=>'Cons', 'enabled'=>1, 'visible'=>-2, 'notnull'=>1, 'position'=>25), 
'anio' =>array('type'=>'smallint(6)', 'label'=>'Anio', 'enabled'=>1, 'visible'=>-2, 'notnull'=>1, 'position'=>30), 
'mes' =>array('type'=>'smallint(6)', 'label'=>'Mes', 'enabled'=>1, 'visible'=>-2, 'notnull'=>1, 'position'=>35), 
'fecha' =>array('type'=>'date', 'label'=>'Fecha', 'enabled'=>1, 'visible'=>-2, 'notnull'=>1, 'position'=>40), 
'concepto' =>array('type'=>'varchar(256)', 'label'=>'Concepto', 'enabled'=>1, 'visible'=>-2, 'notnull'=>1, 'position'=>45), 
'comentario' =>array('type'=>'varchar(150)', 'label'=>'Comentario', 'enabled'=>1, 'visible'=>-2, 'notnull'=>1, 'position'=>50), 
'fk_facture' =>array('type'=>'integer', 'label'=>'Fk facture', 'enabled'=>1, 'visible'=>-2, 'notnull'=>1, 'position'=>55), 
'anombrede' =>array('type'=>'varchar(100)', 'label'=>'Anombrede', 'enabled'=>1, 'visible'=>-2, 'position'=>60), 
'numcheque' =>array('type'=>'varchar(50)', 'label'=>'Numcheque', 'enabled'=>1, 'visible'=>-2, 'position'=>65), 
'ant_ctes' =>array('type'=>'bit(1)', 'label'=>'Ant ctes', 'enabled'=>1, 'visible'=>-2, 'notnull'=>1, 'position'=>70), 
'fechahora' =>array('type'=>'timestamp', 'label'=>'Fechahora', 'enabled'=>1, 'visible'=>-2, 'notnull'=>1, 'position'=>75), 
'societe_type' =>array('type'=>'smallint(6)', 'label'=>'Societe type', 'enabled'=>1, 'visible'=>-2, 'position'=>80), 
'perajuste' =>array('type'=>'integer', 'label'=>'Perajuste', 'enabled'=>1, 'visible'=>-2, 'notnull'=>1, 'position'=>85), 
); 
	public $rowid;
	public $entity;
	public $tipo_pol;
	public $cons;
	public $anio;
	public $mes;
	public $fecha;
	public $concepto;
	public $comentario;
	public $fk_facture;
	public $anombrede;
	public $numcheque;
	public $ant_ctes;
    public $fechahora;
    public $societe_type;
	public $perajuste;
	
	//Variables para detalle de poliza
	private $asiento;
	private $cuenta;
	private $debe;
	private $haber;
	private $fk_poliza;
	private $uuid;
	// END MODULEBUILDER PROPERTIES



	// If this object has a subtable with lines

	/**
	 * @var int    Name of subtable line
	 */
	//public $table_element_line = 'polizadet';

	/**
	 * @var int    Field with ID of parent key if this field has a parent
	 */
	//public $fk_element = 'fk_poliza';

	/**
	 * @var int    Name of subtable class that manage subtable lines
	 */
	//public $class_element_line = 'polizaline';

	/**
	 * @var array  Array of child tables (child tables to delete before deleting a record)
	 */
	//protected $childtables=array('polizadet');

	/**
	 * @var polizaLine[]     Array of subtable lines
	 */
	//public $lines = array();

	public $db;


	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $dATAbASE)
	{
		global $conf, $langs, $user;

		$this->db = $dATAbASE;

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

		// Translate some data of arrayofkeyval
		foreach($this->fields as $key => $val)
		{
			if (is_array($this->fields['status']['arrayofkeyval']))
			{
				foreach($this->fields['status']['arrayofkeyval'] as $key2 => $val2)
				{
					$this->fields['status']['arrayofkeyval'][$key2]=$langs->trans($val2);
				}
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
	public function create(User $user, $notrigger = false)
	{
		$qry = "INSERT INTO llx_$this->table_element ";
		$qry.= "(entity,tipo_pol,cons,anio,mes,fecha,concepto,comentario,fk_facture,anombrede,numcheque,ant_ctes,fechahora,societe_type,perajuste) ";
		$qry.= "VALUES($this->entity, ";
		$qry.= "'$this->tipo_pol', ";
		$qry.= "$this->cons, ";
		$qry.= "$this->anio, ";
		$qry.= "$this->mes, ";
		$qry.= "'$this->fecha', ";
		$qry.= "'$this->concepto', ";
		$qry.= "'$this->comentario', ";
		$qry.= "$this->fk_facture, ";
		$qry.= "'$this->anombrede', ";
		$qry.= "'$this->numcheque', ";
		$qry.= "$this->ant_ctes, ";
		$qry.= "'$this->fechahora', ";
		$qry.= "$this->societe_type, ";
		$qry.= "$this->perajuste) ";
		$this->db->query($qry);

		$qry = "SELECT rowid FROM llx_$this->table_element ORDER BY rowid DESC LIMIT 1";
		$resql=$this->db->query($qry);
        if ($resql)
        {
            $obj = $this->db->fetch_object($resql);
            if ($obj)
            {
                $resp = $obj->rowid;
            }
		}
		return $resp;
	}

	public function crearPoliza()
	{
		$qry = "SELECT t.rowid, t.anio, t.mes, t.estado, t.validado_bg, t.validado_bc, t.validado_er,";
        $qry.= " t.validado_ld, t.validado_lm FROM ".MAIN_DB_PREFIX."contab_periodos as t ";
        $qry .= " WHERE t.anio = '$this->anio'  AND t.mes = '$this->mes' ";
		//return $sqn;
		$resql=$this->db->query($qry);
		if ($resql)
 		{
			$fnrw=$this->db->num_rows($resql);
			$mos=1;
    		if($fnrw>0){
    			$frs=$this->db->fetch_object($resql);
    			if($frs->estado==1){
                	$mos=1;
				}
				else
				{
    				$mos=0;
    			}
        	}
        	else
        	{
            	$mos=2;
        	}
		}
		else
        {
           	$mos=2;
        }
        
        //mos es el estado del periodo 1 = abierto, 0 = cerrado, 2 = periodo no existe;
    	
        switch ($mos)
        {
            case 2:
                return false;//"No se puede crear una poliza en un perdiodo contable que no existe";
                break;
            case 0:
                return false;//"No se puede crear una poliza en un perdiodo contable ya cerrado";
                break;
            case 1:
                return true;//$this->c_polD($db,$anno,$mes,$dia,$id,$conceptos,$f,$compImpTot,$nombreEmisor);
                break;
            default:
                return false;//"Ocurrio una excepcion";
                break;
        }
	}

	public function u_campoPoliza($conceptos,$impuestos,$rfc,$nom,$total,$user,$folio)
    {
		$contador = 0;
		$this->uuid = $folio;
		//Crear asiento por cada concepto
		foreach($conceptos as $con)
		{
			$qry = "SELECT cuenta FROM `llx_autotran_poliza_ia` ";
			$qry.= "WHERE concepto LIKE '%" . $con['Descripcion'] . "%' ";
			$qry.= "AND terceroRFC = '$rfc' ";
			$qry.= "ORDER BY cuenta DESC LIMIT 1";
			$resql=$this->db->query($qry);
			if($resql)
            {
				
                $obj = $this->db->fetch_object($resql);
                if ($obj)
                {
                    $this->cuenta  = $obj->cuenta;
				}
				else
				{
					$qry = "SELECT cta as cuenta FROM llx_contab_cat_ctas ";
					$qry.= "WHERE descta LIKE '%".$con['Descripcion']."%' ORDER BY cta DESC LIMIT 1";
					unset($resql);
					unset($obj);
					$resql=$this->db->query($qry);
					if($resql)
            		{
                		$obj = $this->db->fetch_object($resql);
                		if ($obj)
                		{
                    		$this->cuenta  = $obj->cuenta;
						}
						else
						{
							$qry = "SELECT cta as cuenta FROM llx_contab_cat_ctas ";
							$qry.= "WHERE descta LIKE '%Compras Nacionales%' ORDER BY cta ASC LIMIT 1";
							unset($resql);
							unset($obj);
							$resql = $this->db->query($qry);
							if($resql)
            				{	
								$obj = $this->db->fetch_object($resql);
								if ($obj)
								{
									$this->cuenta  = $obj->cuenta;
								}
								
							}
						}
					}
				}
			}
			$contador++;
			$this->asiento = strval($contador);
			$this->debe = $con['Importe'];
			$this->haber = 0;
			$this->desc = $con['Descripcion'];
			$this->fk_poliza = $this->rowid;
			$this->crearAsiento($user);
            unset($cuenta);
            unset($debe);
            unset($haber);
			unset($desc);
			unset($resql);
			unset($obj);
			unset($this->cuenta);
			unset($this->desc);

		}

		//asiento de impuestos
		if($impuestos != null)
		{
			$debei = 0;
			$this->debe = 0;
			foreach($impuestos as $imp)
			{
				if($imp['Impuesto'] == '002')
				{
					$this->debe = $this->debe + doubleval($imp['Importe']);
				}
				else
				{
					$debei = $debei + doubleval($imp['Importe']);
				}
			}
			if($this->debe > 0)
			{
				$qry = "SELECT cta FROM llx_contab_cat_ctas ";
				$qry.= "WHERE descta LIKE '%IVA pendiente de pago%' ";
				$qry.= "ORDER BY cta ASC LIMIT 1";
				$resql=$this->db->query($qry);
				if($resql)
            	{
            		$obj = $this->db->fetch_object($resql);
               		if ($obj)
               		{
                   		$this->cuenta  = $obj->cta;
               		}
				}
				$this->desc = 'IVA';

				$contador++;
				$this->asiento = $contador;
				$this->crearAsiento($user);
				unset($this->cuenta);
				unset($this->desc);
			}
			if($debei > 0)
			{
				$qry = "SELECT cta FROM llx_contab_cat_ctas ";
				$qry.= "WHERE descta LIKE '%IEPS pendiente de pago%' ";
				$qry.= "ORDER BY cta ASC LIMIT 1";
				$resql=$this->db->query($qry);
				if($resql)
            	{
            		$obj = $this->db->fetch_object($resql);
               		if ($obj)
               		{
                   		$this->cuenta  = $obj->cta;
               		}
				}
				$this->desc = 'IEPS';
				$this->debe = $debei;
				$contador++;
				$this->asiento = $contador;
				$this->crearAsiento($user);
				unset($this->cuenta);
				unset($this->desc);
			}
			unset($this->debe);
			unset($this->haber);
		}
		//Asiento Proveedor
		$contador++;
		$qry = "SELECT cuenta FROM `llx_autotran_poliza_ia` ";
		$qry.= "WHERE concepto LIKE '%$nom%' ";
		$qry.= "AND terceroRFC = '$rfc' ";
		$qry.= "ORDER BY cuenta DESC LIMIT 1";
		$resql=$this->db->query($qry);
		if($resql)
        {
           	$obj = $this->db->fetch_object($resql);
          	if ($obj)
           	{
               	$this->cuenta  = $obj->cuenta;
			}
			else
			{
				$qry = "SELECT cta as cuenta FROM llx_contab_cat_ctas ";
				$qry.= "WHERE descta LIKE '%$nom%' ORDER BY cta DESC LIMIT 1";
				$resql=$this->db->query($qry);
				if($resql)
        		{
           			$obj = $this->db->fetch_object($resql);
          			if ($obj)
           			{
               			$this->cuenta  = $obj->cuenta;
					}
					else
					{
						$qry = "SELECT cta as cuenta FROM llx_contab_cat_ctas ";
						$qry.= "WHERE descta LIKE '%Proveedores nacionales%' ORDER BY cta ASC LIMIT 1";
						$resql=$this->db->query($qry);
						if($resql)
            			{
                			$obj = $this->db->fetch_object($resql);
                			if ($obj)
                			{
                   				$this->cuenta  = $obj->cuenta;
                			}
						}
					}
				}
			}
		}
		
		$this->debe = 0;
		$this->haber = doubleval($total);
		$this->asiento = strval($contador);
		$this->desc = $nom;
		$this->crearAsiento($user);
	}
	
	function crearAsiento($user, $notrigger=0, $tbl = 'contab_polizasdet')
    {
    	global $conf, $langs,$db;
		$error=0;


        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX.$tbl." (";
		
		$sql.= "asiento,";
		$sql.= "cuenta,";
		$sql.= "debe,";
		$sql.= "haber,";
        $sql.= "descripcion,";
        $sql.= "uuid,";
		$sql.= "fk_poliza";
        $sql.= ") VALUES (";
        $sql.= "$this->asiento,";
		$sql.= "'$this->cuenta',";
		$sql.= " $this->debe,";
		$sql.= " $this->haber,";
        $sql.= "'$this->desc',";
        $sql.= "'$this->uuid',";
		$sql.= "$this->fk_poliza";
		$sql.= ");";
       	print_r($sql);
		$this->db->begin();
	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."contab_polizasdet");
        }

        // Commit or rollback
        if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."::create ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
            return $this->id;
		}
    }
	/**
	 * Clone and object into another one
	 *
	 * @param  	User 	$user      	User that creates
	 * @param  	int 	$fromid     Id of object to clone
	 * @return 	mixed 				New object created, <0 if KO
	 */
	public function createFromClone(User $user, $fromid)
	{
		global $langs, $hookmanager, $extrafields;
	    $error = 0;

	    dol_syslog(__METHOD__, LOG_DEBUG);

	    $object = new self($this->db);

	    $this->db->begin();

	    // Load source object
	    $object->fetchCommon($fromid);
	    // Reset some properties
	    unset($object->id);
	    unset($object->fk_user_creat);
	    unset($object->import_key);

	    // Clear fields
	    $object->ref = "copy_of_".$object->ref;
	    $object->title = $langs->trans("CopyOf")." ".$object->title;
	    // ...
	    // Clear extrafields that are unique
	    if (is_array($object->array_options) && count($object->array_options) > 0)
	    {
	    	$extrafields->fetch_name_optionals_label($this->element);
	    	foreach($object->array_options as $key => $option)
	    	{
	    		$shortkey = preg_replace('/options_/', '', $key);
	    		if (! empty($extrafields->attributes[$this->element]['unique'][$shortkey]))
	    		{
	    			//var_dump($key); var_dump($clonedObj->array_options[$key]); exit;
	    			unset($object->array_options[$key]);
	    		}
	    	}
	    }

	    // Create clone
		$object->context['createfromclone'] = 'createfromclone';
	    $result = $object->createCommon($user);
	    if ($result < 0) {
	        $error++;
	        $this->error = $object->error;
	        $this->errors = $object->errors;
	    }

	    unset($object->context['createfromclone']);

	    // End
	    if (!$error) {
	        $this->db->commit();
	        return $object;
	    } else {
	        $this->db->rollback();
	        return -1;
	    }
	}

	/**
	 * Load object in memory from the database
	 *
	 * @param int    $id   Id object
	 * @param string $ref  Ref
	 * @return int         <0 if KO, 0 if not found, >0 if OK
	 */
	public function fetch($id, $ref = null)
	{
		$result = $this->fetchCommon($id, $ref);
		if ($result > 0 && ! empty($this->table_element_line)) $this->fetchLines();
		return $result;
	}

	/**
	 * Load object lines in memory from the database
	 *
	 * @return int         <0 if KO, 0 if not found, >0 if OK
	 */
	/*public function fetchLines()
	{
		$this->lines=array();

		// Load lines with object polizaLine

		return count($this->lines)?1:0;
	}*/

	/**
	 * Load list of objects in memory from the database.
	 *
	 * @param  string      $sortorder    Sort Order
	 * @param  string      $sortfield    Sort field
	 * @param  int         $limit        limit
	 * @param  int         $offset       Offset
	 * @param  array       $filter       Filter array. Example array('field'=>'valueforlike', 'customurl'=>...)
	 * @param  string      $filtermode   Filter mode (AND or OR)
	 * @return array|int                 int <0 if KO, array of pages if OK
	 */
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter=array(), $filtermode='AND')
	{
		global $conf;

		dol_syslog(__METHOD__, LOG_DEBUG);

		$records=array();

		$sql = 'SELECT';
		$sql .= ' t.rowid';
		// TODO Get all fields
		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element. ' as t';
		$sql .= ' WHERE t.entity = '.$conf->entity;
		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				if ($key=='t.rowid') {
					$sqlwhere[] = $key . '='. $value;
				}
				elseif (strpos($key,'date') !== false) {
					$sqlwhere[] = $key.' = \''.$this->db->idate($value).'\'';
				}
				elseif ($key=='customsql') {
					$sqlwhere[] = $value;
				}
				else {
					$sqlwhere[] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
				}
			}
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' AND (' . implode(' '.$filtermode.' ', $sqlwhere).')';
		}

		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield, $sortorder);
		}
		if (!empty($limit)) {
			$sql .=  ' ' . $this->db->plimit($limit, $offset);
		}

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql))
			{
				$record = new self($this->db);

				$record->id = $obj->rowid;
				// TODO Get other fields

				//var_dump($record->id);
				$records[$record->id] = $record;
			}
			$this->db->free($resql);

			return $records;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return -1;
		}
	}

	/**
	 * Update object into database
	 *
	 * @param  User $user      User that modifies
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 * @return int             <0 if KO, >0 if OK
	 */
	public function update(User $user, $notrigger = false)
	{
		return $this->updateCommon($user, $notrigger);
	}

	/**
	 * Delete object in database
	 *
	 * @param User $user       User that deletes
	 * @param bool $notrigger  false=launch triggers after, true=disable triggers
	 * @return int             <0 if KO, >0 if OK
	 */
	public function delete(User $user, $notrigger = false)
	{
		return $this->deleteCommon($user, $notrigger);
		//return $this->deleteCommon($user, $notrigger, 1);
	}

	/**
	 *  Return a link to the object card (with optionaly the picto)
	 *
	 *	@param	int		$withpicto					Include picto in link (0=No picto, 1=Include picto into link, 2=Only picto)
	 *	@param	string	$option						On what the link point to ('nolink', ...)
     *  @param	int  	$notooltip					1=Disable tooltip
     *  @param  string  $morecss            		Add more css on link
     *  @param  int     $save_lastsearch_value    	-1=Auto, 0=No save of lastsearch_values when clicking, 1=Save lastsearch_values whenclicking
	 *	@return	string								String with URL
	 */
	function getNomUrl($withpicto=0, $option='', $notooltip=0, $morecss='', $save_lastsearch_value=-1)
	{
		global $db, $conf, $langs, $hookmanager;
        global $dolibarr_main_authentication, $dolibarr_main_demo;
        global $menumanager;

        if (! empty($conf->dol_no_mouse_hover)) $notooltip=1;   // Force disable tooltips

        $result = '';

        $label = '<u>' . $langs->trans("poliza") . '</u>';
        $label.= '<br>';
        $label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

        $url = dol_buildpath('/descargasat/poliza_card.php',1).'?id='.$this->id;

        if ($option != 'nolink')
        {
	        // Add param to save lastsearch_values or not
	        $add_save_lastsearch_values=($save_lastsearch_value == 1 ? 1 : 0);
	        if ($save_lastsearch_value == -1 && preg_match('/list\.php/',$_SERVER["PHP_SELF"])) $add_save_lastsearch_values=1;
	        if ($add_save_lastsearch_values) $url.='&save_lastsearch_values=1';
        }

        $linkclose='';
        if (empty($notooltip))
        {
            if (! empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER))
            {
                $label=$langs->trans("Showpoliza");
                $linkclose.=' alt="'.dol_escape_htmltag($label, 1).'"';
            }
            $linkclose.=' title="'.dol_escape_htmltag($label, 1).'"';
            $linkclose.=' class="classfortooltip'.($morecss?' '.$morecss:'').'"';

            /*
             $hookmanager->initHooks(array('polizadao'));
             $parameters=array('id'=>$this->id);
             $reshook=$hookmanager->executeHooks('getnomurltooltip',$parameters,$this,$action);    // Note that $action and $object may have been modified by some hooks
             if ($reshook > 0) $linkclose = $hookmanager->resPrint;
             */
        }
        else $linkclose = ($morecss?' class="'.$morecss.'"':'');

		$linkstart = '<a href="'.$url.'"';
		$linkstart.=$linkclose.'>';
		$linkend='</a>';

		$result .= $linkstart;
		if ($withpicto) $result.=img_object(($notooltip?'':$label), ($this->picto?$this->picto:'generic'), ($notooltip?(($withpicto != 2) ? 'class="paddingright"' : ''):'class="'.(($withpicto != 2) ? 'paddingright ' : '').'classfortooltip"'), 0, 0, $notooltip?0:1);
		if ($withpicto != 2) $result.= $this->ref;
		$result .= $linkend;
		//if ($withpicto != 2) $result.=(($addlabel && $this->label) ? $sep . dol_trunc($this->label, ($addlabel > 1 ? $addlabel : 0)) : '');

		global $action,$hookmanager;
		$hookmanager->initHooks(array('polizadao'));
		$parameters=array('id'=>$this->id, 'getnomurl'=>$result);
		$reshook=$hookmanager->executeHooks('getNomUrl',$parameters,$this,$action);    // Note that $action and $object may have been modified by some hooks
		if ($reshook > 0) $result = $hookmanager->resPrint;
		else $result .= $hookmanager->resPrint;

		return $result;
	}

	/**
	 *  Return label of the status
	 *
	 *  @param  int		$mode          0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 6=Long label + Picto
	 *  @return	string 			       Label of status
	 */
	public function getLibStatut($mode=0)
	{
		return $this->LibStatut($this->status, $mode);
	}

    // phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *  Return the status
	 *
	 *  @param	int		$status        Id status
	 *  @param  int		$mode          0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 6=Long label + Picto
	 *  @return string 			       Label of status
	 */
	public function LibStatut($status, $mode=0)
	{
		// phpcs:enable
		if (empty($this->labelstatus))
		{
			global $langs;
			//$langs->load("descargasat");
			$this->labelstatus[1] = $langs->trans('Enabled');
			$this->labelstatus[0] = $langs->trans('Disabled');
		}

		if ($mode == 0)
		{
			return $this->labelstatus[$status];
		}
		elseif ($mode == 1)
		{
			return $this->labelstatus[$status];
		}
		elseif ($mode == 2)
		{
			if ($status == 1) return img_picto($this->labelstatus[$status],'statut4', '', false, 0, 0, '', 'valignmiddle').' '.$this->labelstatus[$status];
			elseif ($status == 0) return img_picto($this->labelstatus[$status],'statut5', '', false, 0, 0, '', 'valignmiddle').' '.$this->labelstatus[$status];
		}
		elseif ($mode == 3)
		{
			if ($status == 1) return img_picto($this->labelstatus[$status],'statut4', '', false, 0, 0, '', 'valignmiddle');
			elseif ($status == 0) return img_picto($this->labelstatus[$status],'statut5', '', false, 0, 0, '', 'valignmiddle');
		}
		elseif ($mode == 4)
		{
			if ($status == 1) return img_picto($this->labelstatus[$status],'statut4', '', false, 0, 0, '', 'valignmiddle').' '.$this->labelstatus[$status];
			elseif ($status == 0) return img_picto($this->labelstatus[$status],'statut5', '', false, 0, 0, '', 'valignmiddle').' '.$this->labelstatus[$status];
		}
		elseif ($mode == 5)
		{
			if ($status == 1) return $this->labelstatus[$status].' '.img_picto($this->labelstatus[$status],'statut4', '', false, 0, 0, '', 'valignmiddle');
			elseif ($status == 0) return $this->labelstatus[$status].' '.img_picto($this->labelstatus[$status],'statut5', '', false, 0, 0, '', 'valignmiddle');
		}
		elseif ($mode == 6)
		{
			if ($status == 1) return $this->labelstatus[$status].' '.img_picto($this->labelstatus[$status],'statut4', '', false, 0, 0, '', 'valignmiddle');
			elseif ($status == 0) return $this->labelstatus[$status].' '.img_picto($this->labelstatus[$status],'statut5', '', false, 0, 0, '', 'valignmiddle');
		}
	}

	/**
	 *	Charge les informations d'ordre info dans l'objet commande
	 *
	 *	@param  int		$id       Id of order
	 *	@return	void
	 */
	public function info($id)
	{
		$sql = 'SELECT rowid, date_creation as datec, tms as datem,';
		$sql.= ' fk_user_creat, fk_user_modif';
		$sql.= ' FROM '.MAIN_DB_PREFIX.$this->table_element.' as t';
		$sql.= ' WHERE t.rowid = '.$id;
		$result=$this->db->query($sql);
		if ($result)
		{
			if ($this->db->num_rows($result))
			{
				$obj = $this->db->fetch_object($result);
				$this->id = $obj->rowid;
				if ($obj->fk_user_author)
				{
					$cuser = new User($this->db);
					$cuser->fetch($obj->fk_user_author);
					$this->user_creation   = $cuser;
				}

				if ($obj->fk_user_valid)
				{
					$vuser = new User($this->db);
					$vuser->fetch($obj->fk_user_valid);
					$this->user_validation = $vuser;
				}

				if ($obj->fk_user_cloture)
				{
					$cluser = new User($this->db);
					$cluser->fetch($obj->fk_user_cloture);
					$this->user_cloture   = $cluser;
				}

				$this->date_creation     = $this->db->jdate($obj->datec);
				$this->date_modification = $this->db->jdate($obj->datem);
				$this->date_validation   = $this->db->jdate($obj->datev);
			}

			$this->db->free($result);
		}
		else
		{
			dol_print_error($this->db);
		}
	}

	/**
	 * Initialise object with example values
	 * Id must be 0 if object instance is a specimen
	 *
	 * @return void
	 */
	public function initAsSpecimen()
	{
		$this->initAsSpecimenCommon();
	}


	/**
	 * Action executed by scheduler
	 * CAN BE A CRON TASK. In such a case, paramerts come from the schedule job setup field 'Parameters'
	 *
	 * @return	int			0 if OK, <>0 if KO (this function is used also by cron so only 0 is OK)
	 */
	//public function doScheduledJob($param1, $param2, ...)
	public function doScheduledJob()
	{
		global $conf, $langs;

		//$conf->global->SYSLOG_FILE = 'DOL_DATA_ROOT/dolibarr_mydedicatedlofile.log';

		$error = 0;
		$this->output = '';
		$this->error='';

		dol_syslog(__METHOD__, LOG_DEBUG);

		$now = dol_now();

		$this->db->begin();

		// ...

		$this->db->commit();

		return $error;
	}
}

/**
 * Class polizaLine. You can also remove this and generate a CRUD class for lines objects.
 */
/*
class polizaLine
{
	// @var int ID
	public $id;
	// @var mixed Sample line property 1
	public $prop1;
	// @var mixed Sample line property 2
	public $prop2;
}
*/
?>