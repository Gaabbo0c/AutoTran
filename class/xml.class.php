<?php

require_once  '../admin/helper/conexionex.php';

class xml
{
    public $fields = array( "Version",
                        "Fecha" ,
                        "Sello" ,
                        "Total" ,
                        "Subtotal",
                        "Certificado",
                        "MetodoPago" ,
                        "FormaPago" ,
                        "CondicionesDePago" ,
                        "NoCertificado" ,
                        "TipoDeComprobante" ,
                        "Serie" ,
                        "Folio" ,
                        "Moneda" ,
                        "Descuento" ,
                        "EmisorRFC" ,
                        "NombreEmisor" ,
                        "RegimenFiscal" ,
                        "ReceptorRFC" ,
                        "NombreReceptor" ,
                        "UsoCFDI" ,
                        "Concepto" ,
                        "ConceptoTras" ,
                        "IEPS"  ,
                        "Retencion" ,
                        "RetencionISR" ,
                        "RetencionLocal" ,
                        "Traslados" ,
                        "TotalImpuestos" ,
                        "TimbreVersion" ,
                        "TimbreUUID" ,
                        "FechaTimbre" ,
                        "ProveedorRFC" ,
                        "SelloCFD" ,
                        "NoCertificadoSAT" ,
                        "SelloSAT" );

    public $Version;
    public $Fecha;
    public $Sello;
    public $Total;
    public $Subtotal;
    public $Certificado;
    public $MetodoPago;
    public $FormaPago;
    public $CondicionesDePago;
    public $NoCertificado;
    public $TipoDeComprobante;
    public $Serie;
    public $Folio;
    public $Moneda;
    public $Descuento;
    public $EmisorRFC;
    public $NombreEmisor;
    public $RegimenFiscal;
    public $ReceptorRFC;
    public $NombreReceptor;
    public $UsoCFDI;
    public $Conceptos;
    public $ConceptoTraslado;
    public $IEPS;
    public $Retencion;
    public $RetencionISR;
    public $RetencionLocal;
    public $Traslados;
    public $TotalImpuestos;
    public $TimbreVersion;
    public $TimbreUUID;
    public $FechaTimbre;
    public $ProveedorRFC;
    public $SelloCFD;
    public $NoCertificadoSAT;
    public $SelloSAT;
    
    //leer xml cfdi 3.3
    public function leer($xml)
	{
        $sxe = new SimpleXMLElement($xml);
        $ns = $sxe->getNamespaces(true);
        $sxe->registerXPathNamespace('c', $ns['cfdi']);
        $sxe->registerXPathNamespace('t', $ns['tfd']);
        $sxe->registerXPathNamespace('i', $ns['implocal']);

        foreach ($sxe->xpath('//cfdi:Comprobante') as $cfdiComprobante)
        {
            $Version=$cfdiComprobante['Version'];
            if ($Version != "3.3") 
            {
                print "<strong style='color:#F00;'>Error: El archivo XML no es versión 3.3</strong>";
                die;
            }
            $Fecha=$cfdiComprobante['Fecha']; 
            $Sello=$cfdiComprobante['Sello']; 
            $Total=floatval($cfdiComprobante['Total']);
            $Subtotal=floatval($cfdiComprobante['SubTotal']);
            $Certificado=$cfdiComprobante['Certificado']; 
            $MetodoPago=trim($cfdiComprobante['MetodoPago']);
            $FormaPago=$cfdiComprobante['FormaPago'];
            $CondicionesDePago=$cfdiComprobante['CondicionesDePago'];
            $NoCertificado=$cfdiComprobante['NoCertificado'];
            $TipoDeComprobante=$cfdiComprobante['TipoDeComprobante'];
            $Serie=$cfdiComprobante['Serie'];
            $Folio=$cfdiComprobante['Folio'];
            $Moneda=$cfdiComprobante['Moneda'];
            if(! empty($cfdiComprobante['descuento'])) $Descuento=$cfdiComprobante['descuento'];
            else if(! empty($cfdiComprobante['Descuento'])) $Descuento=$cfdiComprobante['Descuento'];
        }

        foreach ($sxe->xpath('//cfdi:Comprobante//cfdi:Emisor') as $Emisor) 
        {
            $EmisorRFC=$Emisor['Rfc'];
            $NombreEmisor=(! empty($Emisor['Nombre']))?trim($Emisor['Nombre']):trim($Emisor['Rfc']);
            $RegimenFiscal=$Emisor['RegimenFiscal'];
        }

        foreach ($sxe->xpath('//cfdi:Comprobante//cfdi:Receptor') as $Receptor) 
        {
            $ReceptorRFC=$Receptor['Rfc'];
            $NombreReceptor=(! empty($Receptor['Nombre']))?$Receptor['Nombre']:"Sin razón social";
            $UsoCFDI=$Receptor['UsoCFDI'];
        }

        $i=0;
        foreach ($sxe->xpath('//cfdi:Comprobante//cfdi:Conceptos//cfdi:Concepto') as $Concepto)
        {
            $Conceptos[$i]['NoIdentificacion']=$Concepto['NoIdentificacion'] != "" ? $Concepto['NoIdentificacion'] : "";
            $Conceptos[$i]['IDprod']=0;
            $Conceptos[$i]['ClaveProdServ']=$Concepto['ClaveProdServ'];
            $Conceptos[$i]['Cantidad']=floatval($Concepto['Cantidad']);
            $Conceptos[$i]['ClaveUnidad']=$Concepto['ClaveUnidad'];
            $Conceptos[$i]['Unidad']=$Concepto['Unidad'];
            $Conceptos[$i]['Descripcion']=$Concepto['Descripcion'];
            $Conceptos[$i]['ValorUnitario']=floatval($Concepto['ValorUnitario']);
            $Conceptos[$i]['Importe']=floatval($Concepto['Importe']);
            $Conceptos[$i]['BaseType']="HT";
            $i++;
        }
        $i=0;
        $m=0;
        foreach ($sxe->xpath('//cfdi:Comprobante//cfdi:Conceptos//cfdi:Concepto//cfdi:Impuestos//cfdi:Traslados//cfdi:Traslado') as $ConceptoTras) 
        {
            if($ConceptoTras['Impuesto']=="002")
            {
        	$ConceptoTraslado[$i]['Base']=floatval($ConceptoTras['Base']);
            $ConceptoTraslado[$i]['Impuesto']=$ConceptoTras['Impuesto'];
            $ConceptoTraslado[$i]['TipoFactor']=$ConceptoTras['TipoFactor'];
            $ConceptoTraslado[$i]['TasaOCuota']=floatval($ConceptoTras['TasaOCuota']);
            $ConceptoTraslado[$i]['Importe']=floatval($ConceptoTras['Importe']);
            $i++;
            }
            if($ConceptoTras['Impuesto']=="003")
            {
            	$IEPS[$m]['Impuesto']=$ConceptoTras['Impuesto'];
            	$IEPS[$m]['Importe']=floatval($ConceptoTras['Importe']);
            	$m++;
            }
        }
        $i=0;
        $m=0;
        foreach ($sxe->xpath('//cfdi:Comprobante//cfdi:Impuestos//cfdi:Retenciones//cfdi:Retencion') as $Retenc) 
        {
            if($Retenc['Impuesto']=="002" && ($Retenc['Base']==NULL))
            {
            $Retencion[$i]['Impuesto']=$Retenc['Impuesto'];
            $Retencion[$i]['Importe']=floatval($Retenc['Importe']);
            $i++;
            }
            if($Retenc['Impuesto']=="001"  && ($Retenc['Base']==NULL))
            {
                $RetencionISR[$m]['Impuesto']=$Retenc['Impuesto'];
                $RetencionISR[$m]['Importe']=floatval($Retenc['Importe']);
                $m++;
            }
        }
        $i=0;
        foreach ($sxe->xpath('//i:ImpuestosLocales') as $RetencLocales) 
        {
            $RetencionLocal[$i]['Importe']=floatval($RetencLocales['TotaldeRetenciones']);
            $i++;
        }
        $i=0;
        foreach ($sxe->xpath('//cfdi:Comprobante//cfdi:Impuestos//cfdi:Traslados//cfdi:Traslado') as $Traslado) 
        {
            $Traslados[$i]['Base']=floatval($Traslado['Base']);
            $Traslados[$i]['Impuesto']=$Traslado['Impuesto'];
            $Traslados[$i]['TipoFactor']=$Traslado['TipoFactor'];
            $Traslados[$i]['TasaOCuota']=floatval($Traslado['TasaOCuota']);
            $Traslados[$i]['Importe']=floatval($Traslado['Importe']);
            $i++;
        }
        $i=0;
        foreach ($sxe->xpath('//cfdi:Comprobante//cfdi:Impuestos') as $impuestotot) 
        {
            if(floatval($impuestotot['TotalImpuestosTrasladados'])>0)
            {
                $TotalImpuestos[$i]['TotalImpuestosTrasladados']=floatval($impuestotot['TotalImpuestosTrasladados']);
                $i++;
            }
        }
        foreach ($sxe->xpath('//t:TimbreFiscalDigital') as $tfd) 
        {
            $TimbreVersion=$tfd['Version'];
            $TimbreUUID=$tfd['UUID'];
            $FechaTimbre=$tfd['FechaTimbrado'];
            $ProveedorRFC=$tfd['RfcProvCertif'];
            $SelloCFD=$tfd['SelloCFD'];
            $NoCertificadoSAT=$tfd['NoCertificadoSAT'];
            $SelloSAT=$tfd['SelloSAT'];
        }

        $leido = array( "Version" => $Version,
                        "Fecha" => $Fecha,
                        "Sello" => $Sello,
                        "Total" => $Total,
                        "Subtotal" => $Subtotal,
                        "Certificado" => $Certificado,
                        "MetodoPago" => $MetodoPago,
                        "FormaPago" => $FormaPago,
                        "CondicionesDePago" => $CondicionesDePago,
                        "NoCertificado" => $NoCertificado,
                        "TipoDeComprobante" => $TipoDeComprobante,
                        "Serie" => $Serie,
                        "Folio" => $Folio,
                        "Moneda" => $Moneda,
                        "Descuento" => $Descuento,
                        "EmisorRFC" => $EmisorRFC,
                        "NombreEmisor" => $NombreEmisor,
                        "RegimenFiscal" => $RegimenFiscal,
                        "ReceptorRFC" => $ReceptorRFC,
                        "NombreReceptor" => $NombreReceptor,
                        "UsoCFDI" => $UsoCFDI,
                        "Conceptos" => $Conceptos,
                        "ConceptoTraslado" => $ConceptoTraslado,
                        "IEPS"  => $IEPS,
                        "Retencion" => $Retencion,
                        "RetencionISR" => $RetencionISR,
                        "RetencionLocal" => $RetencionLocal,
                        "Traslados" => $Traslados,
                        "TotalImpuestos" => $TotalImpuestos,
                        "TimbreVersion" => $TimbreVersion,
                        "TimbreUUID" => $TimbreUUID,
                        "FechaTimbre" => $FechaTimbre,
                        "ProveedorRFC" => $ProveedorRFC,
                        "SelloCFD" => $SelloCFD,
                        "NoCertificadoSAT" => $NoCertificadoSAT,
                        "SelloSAT" => $SelloSAT);

    	return $leido;
    }

    public function registrarXML($id,$db)
    {
         
        
        $qry  = "SELECT af.FolioFiscal AS folio, ff.ref as factura FROM llx_facture_fourn AS ff ";
        $qry .= "INNER JOIN llx_autotran_facturas AS af ";
        $qry .= "ON af.Factura = ff.ref ";
        $qry .= "WHERE ff.rowid = {$id}";

        $resql=$db->query($qry);
        if ($resql)
        {
            $obj = $db->fetch_object($resql);
            if ($obj)
            {
                $folio   = $obj->folio;
                $factura = $obj->factura;
            }
        }
        $conn = new conexionex();

        $qry  = "SELECT xml FROM sys_autotran_xml WHERE uuid = '{$folio}' LIMIT 1";
        $xml = $conn->consultarTabla($qry);

        $qry  = "INSERT INTO llx_cfdimx ";
        $qry .= "(factura_seriefolio, xml, uuid)";
        $qry .= "VALUES ('{$factura}', '{$xml['xml']}', '{$folio}');";

        $db->query($qry);
    }

}
?>