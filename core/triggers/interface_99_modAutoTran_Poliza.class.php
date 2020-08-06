<?php
/* Copyright (C) Gabriel Carpio <gabriel@quantumbit.mx>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

 /**
 * \file    autotran/triggers/interface_99_modAutoTran_Poliza.class.php
 * \ingroup autotran
 *
 * Crea polizas al generar facturas
 *
 */

require_once DOL_DOCUMENT_ROOT.'/core/triggers/dolibarrtriggers.class.php';
include_once DOL_DOCUMENT_ROOT."/autotran/class/poliza.class.php";
include_once DOL_DOCUMENT_ROOT."/autotran/class/polizadet.class.php";

class InterfacePoliza extends DolibarrTriggers
{
    protected $db;
    
    public function __construct($db)
	{
		$this->db = $db;

		$this->name = preg_replace('/^Interface/i', '', get_class($this));
		$this->family = "Qbit";
		$this->description = "AutoTran triggers.";
		$this->version = '1.0';
        $this->picto = 'autotran@autotran';
        $this->sup_pay = false;
        $this->id;
    }
    
    public function getName()
	{
		return $this->name;
    }
    
    public function getDesc()
	{
		return $this->description;
    }
    
    public function runTrigger($action, $object, User $user, Translate $langs, Conf $conf)
	{
        if (empty($conf->autotran->enabled)) return 0;

        //switch ($action) 
        //{
          //  case 'BILL_SUPPLIER_PAYED':
            //    $this->crearPoliza($object,$user);   
              //  break;
            //case 'PAYMENT_SUPPLIER_CREATE':
              //  $this->updatePoliza($object);   
               // break;
            //case 'PAYMENT_ADD_TO_BANK':
              //  $this->updateAsiento($object);   
               // break;
            //default:
		      //  break;
        //}
        return 0;
    }
    private function crearPoliza($object,$user)
    {

        $poliza = new poliza($this->db);
        $poliza->entity = $conf->entity;
        $poliza->tipo_pol = "E";
        
        $fecha = date('Y-m-d',$object->tms);
        $fechas = explode('-', $fecha);

        $refFac = $object->ref;

        $qry = "SELECT ff.rowid AS factura, ";
        $qry.= "s.nom AS nombre ";
        $qry.= "FROM llx_facture_fourn AS ff ";
        $qry.= "INNER JOIN llx_societe as s ON ff.fk_soc = s.rowid ";
        $qry.= "WHERE ff.ref = '$refFac'";
        $resql=$this->db->query($qry);
        if ($resql)
        { 
            $obj = $this->db->fetch_object($resql);
            if ($obj)
            {
                $factura = $obj->factura;
                $nombreEmisor = $obj->nombre;
            }
        }

        $poliza->anio = $fechas[0];
        $poliza->mes = $fechas[1];

        $poliza->cons = 0;
        $poliza->fecha = $fecha;
        $poliza->concepto = $nombreEmisor;
        $poliza->comentario = "Generado Automaticamente";
        $poliza->fk_facture = $factura;
        $poliza->anombrede = "";
        $poliza->numcheque = "";
        $poliza->ant_ctes = 0;
        $poliza->fechahora = $fecha;
        $poliza->societe_type = 2;
        $poliza->perajuste = 0;

        if($poliza->crearPoliza())
        {
            $poliza->rowid = $poliza->create($user);
            $polId = intval($poliza->rowid);
            $num = 1;
            for ($i = 1;$i <= 4; $i++) 
            {
                $polizadet = new polizadet($this->db);
                $polizadet->fk_poliza = $polId;
                $polizadet->asiento = $num;
                switch ($i) 
                {
                    case 1:
                        $polizadet->cuenta = 'vacio';
                        $polizadet->debe = 0;
                        $polizadet->haber = 0;
                        $polizadet->descripcion = $nombreEmisor;
                        $num++;
                        break;
                    case 2:
                        $polizadet->cuenta = 'vacio';
                        $polizadet->debe = 0;
                        $polizadet->haber = 0;
                        $polizadet->descripcion = "IVA pendiente de pago"; 
                        $num++;
                        break;
                    case 3:
                        $polizadet->cuenta = 'vacio';
                        $polizadet->debe = 0;
                        $polizadet->haber = 0;
                        $polizadet->descripcion = "IVA acreditable pagado";   
                        $num++;
                        break;
                    case 4:
                        $polizadet->cuenta = 'vacio';
                        $polizadet->debe = 0;
                        $polizadet->haber = 0;
                        $polizadet->descripcion = $nombreEmisor;   
                        break;
                    default:
		                break;
                }

                $qry = "INSERT INTO llx_contab_polizasdet ";
		        $qry.= "(fk_poliza,asiento,cuenta,debe,haber,descripcion) ";
		        $qry.= "VALUES($polizadet->fk_poliza, ";
		        $qry.= "$polizadet->asiento, ";
		        $qry.= "'$polizadet->cuenta', ";
		        $qry.= "$polizadet->debe, ";
		        $qry.= "$polizadet->haber, ";
		        $qry.= "'$polizadet->descripcion')";
		        $this->db->query($qry);
            }
        }
    }

    private function updatePoliza($object)
    {
        
        $fecha = date('Y-m-d',$object->datepaye);
        $fechas = explode('-', $fecha);

        $qry = "UPDATE llx_contab_polizas ";
        $qry.= "SET anio = '$fechas[0]', ";
        $qry.= "mes = '$fechas[1]', ";
        $qry.= "fecha = '$fecha' , ";
        $qry.= "fechahora = '$fecha' ";
        $qry.= "WHERE cons = 0";
        $this->db->query($qry);

        $qry = "SELECT COUNT(*) as cuenta FROM llx_contab_polizas ";
        $qry.= "WHERE mes = '$fechas[1]' AND anio = '$fechas[0]' AND tipo_pol = 'E'";
        $resql=$this->db->query($qry);
        if ($resql)
        { 
            $obj = $this->db->fetch_object($resql);
            if ($obj)
            {
                $cons = intval($obj->cuenta);
            }
        }
        if($cons != 1)
        {
            $cons++;
        }

        $qry = "UPDATE llx_contab_polizas ";
        $qry.= "SET cons = $cons ";
        $qry.= "WHERE cons = 0";
        $this->db->query($qry);
    }

    private function updateAsiento($object)
    {
        foreach($object->amounts as $key => $value)
        {
            $qry = "SELECT ff.total_ht as total , ff.total_tva as iva, s.siren as rfc,s.nom as nombre ";
            $qry.= "FROM llx_facture_fourn as ff ";
            $qry.= "INNER JOIN llx_societe as s ON s.rowid = ff.fk_soc ";
            $qry.= "WHERE ff.rowid = $key";
            $resql=$this->db->query($qry);
            if ($resql)
            { 
                $obj = $this->db->fetch_object($resql);
                if ($obj)
                {
                    $total = floatval($obj->total);
                    $iva = floatval($obj->iva);
                    $rfc = $obj->rfc;
                    $nombre = $obj->nombre;
                }
            }

            $qry = "SELECT rowid ";
            $qry.= "FROM llx_contab_polizas ";
            $qry.= "WHERE fk_facture = $key ";
            $qry.= "AND tipo_pol = 'E' ";
            $qry.= "ORDER BY rowid LIMIT 1";
            $resql=$this->db->query($qry);
            if ($resql)
            { 
                $obj = $this->db->fetch_object($resql);
                if ($obj)
                {
                    $pol = $obj->rowid;
                }
            }

            $qry = "SELECT amount as pago ";
            $qry.= "FROM llx_paiementfourn_facturefourn ";
            $qry.= "WHERE fk_facturefourn = $key";
            $resql= $this->db->query($qry);
            if ($resql)
            {
                $num = $this->db->num_rows($resql);
                $i = 1;
                $pagos = 0;
                if ($num)
                {
                    while ($i < $num)
                    {
                        $obj = $this->db->fetch_object($resql);
                        if ($obj)
                        {
                            $pago = floatval($obj->pago);
                        }
                        $pagos = $pagos + $pago;
                        $i++;
                    }
                }
            }

            $debe = $total - $pagos;
            $cuenta = $this->definirCuenta($nombre,'Compras Nacionales',$rfc);

            $qry = "UPDATE llx_contab_polizasdet ";
            $qry.= "SET cuenta = '$cuenta', ";
            $qry.= "debe = $debe ";
            $qry.= "WHERE asiento = 1 ";
            $qry.= "AND fk_poliza = $pol";
            $this->db->query($qry);
            $coniva = false;
            if($iva > 0)
            {
                $cuenta = $this->definirCuenta("IVA pendiente de pago",'IVA pendiente de pago',$rfc);
                $debe = $iva - ($pagos * 0.16);

                $qry = "UPDATE llx_contab_polizasdet ";
                $qry.= "SET cuenta = '$cuenta', ";
                $qry.= "debe = $debe ";
                $qry.= "WHERE asiento = 2 ";
                $qry.= "AND fk_poliza = $pol";
                $this->db->query($qry);


                $cuenta = $this->definirCuenta("IVA acreditable pagado",'IVA acreditable pagado',$rfc);
                $haber = $iva;
                $qry = "UPDATE llx_contab_polizasdet ";
                $qry.= "SET cuenta = '$cuenta', ";
                $qry.= "haber = $haber ";
                $qry.= "WHERE asiento = 3 ";
                $qry.= "AND fk_poliza = $pol";
                $this->db->query($qry);

                $coniva = true;
            }
            else
            {
                $qry = "DELETE FROM llx_contab_polizasdet ";
                $qry.= "WHERE asiento IN (2,3) ";
                $qry.= "AND fk_poliza = $pol";
                $this->db->query($qry);
            }
            $asiento = 4;
            if(!$coniva)
            {
                $qry = "UPDATE llx_contab_polizasdet ";
                $qry.= "SET asiento = 2 ";
                $qry.= "WHERE asiento = 4 ";
                $qry.= "AND fk_poliza = $pol";
                $this->db->query($qry);
                $asiento = 2;
            }
            $qry = "SELECT label ";
            $qry.= "FROM llx_bank_account ";
            $qry.= "WHERE rowid = $object->fk_account";
            $resql=$this->db->query($qry);
            if ($resql)
            { 
                $obj = $this->db->fetch_object($resql);
                if ($obj)
                {
                    $banco = floatval($obj->total);
                }
            }
            $cuenta = $this->definirCuenta($banco,'Bancos',$rfc);
            $haber = $value;
            $qry = "UPDATE llx_contab_polizasdet ";
            $qry.= "SET cuenta = '$cuenta', ";
            $qry.= "haber = $haber ";
            $qry.= "WHERE asiento = $asiento ";
            $qry.= "AND fk_poliza = $pol";
            $this->db->query($qry);
        }
        
    }

    private function definirCuenta($des,$def,$rfc)
    {
        $qry = "SELECT cuenta FROM `llx_autotran_poliza_ia` ";
		$qry.= "WHERE concepto LIKE '%" . $des . "%' ";
		$qry.= "AND terceroRFC = '$rfc' ";
		$qry.= "ORDER BY cuenta DESC LIMIT 1";
		$resql=$this->db->query($qry);
		if($resql)
        {	
            $obj = $this->db->fetch_object($resql);
            if ($obj)
            {
                $cuenta  = $obj->cuenta;
			}
			else
			{
				$qry = "SELECT cta as cuenta FROM llx_contab_cat_ctas ";
				$qry.= "WHERE descta LIKE '%".$des."%' ORDER BY cta DESC LIMIT 1";
				unset($resql);
				unset($obj);
				$resql=$this->db->query($qry);
				if($resql)
            	{
                	$obj = $this->db->fetch_object($resql);
                	if ($obj)
                	{
                   		$cuenta  = $obj->cuenta;
					}
					else
					{
						$qry = "SELECT cta as cuenta FROM llx_contab_cat_ctas ";
						$qry.= "WHERE descta LIKE '%$def%' ORDER BY cta ASC LIMIT 1";
						unset($resql);
						unset($obj);
						$resql = $this->db->query($qry);
						if($resql)
            			{	
							$obj = $this->db->fetch_object($resql);
							if ($obj)
							{
								$cuenta  = $obj->cuenta;
							}			
                        }
					}
				}
			}
        }
        return $cuenta;
    }
}
?>