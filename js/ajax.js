function Registrar(uuid,factura,rowid,total,rfc)
{
    console.log("Registrando");

    var parametros = 
    {
        "FolioFiscal" : uuid,
        "Factura" : factura,
        "Total"   : total,
        "rowId"   : rowid
    };
    console.log("Parametros creados");
    var promise = $.ajax(
        {
            data: parametros,
            url:  'Factura/registrar_factura.php',
            type: 'post',
            beforeSend: function() {
                $("#respuesta"+rowid).html("<img src=\"img/loader1.gif\"/> Por favor espera un momento");
                //actualizar();
            },
            success: function(response){
                $("#respuesta"+rowid).html("");
                //actualizar();
            }
        }
    );
        promise.then(function(resp)
        {
            console.log(resp);
            down(rfc,uuid,resp,rowid,factura);
                
        }
        );
        
}
function actualizar()
{
    location.reload(true);
}
function QuitarFactura(rowid)
{
    console.log("Quitando factura ligada " + rowid);
    var prms = 
    {
    "Id" : rowid
    }
    $.ajax(
        {
            data: prms,
            url:  'Factura/quitar_factura.php',
            type: 'post',
            beforeSend: function() {
                $("#respuesta"+rowid).html("<img src=\"img/loader1.gif\"/> Por favor espera un momento");
                //actualizar();
            },
            success: function(response){
                $("#respuesta"+rowid).html(response);
                console.log(response);
                //actualizar();
            }
        }
    );
}

function down(rfc,fileName,variable,rowId,factura)
{
    console.log("Descargando " + rfc);
    var params = 
    {
        "\"RFC\"": rfc,
        "\"FileName\"": fileName + ".xml",
    }
    console.log(params);
    promise = fetch('http://robotran.ddns.net:7004/api/download',
    {
        headers: { "Content-Type": "application/json; charset=utf-8" ,"Access-Control-Allow-Origin": "*" },
        method: 'POST',
        body: JSON.stringify
        ({
            "RFC": rfc,
            "FileName": fileName + ".xml",
        })
    });
    promise.then ((resp)=> resp.text())
    .then(xmlString => $.parseXML(xmlString))
    .then(function(dats)
    {
        console.log("recibio xml");
        
        var xmlText = new XMLSerializer().serializeToString(dats);
        console.log(variable);
        if(true)
        {
            var archivo = 
            {
                "xml": xmlText
            }
            console.log("API correcta");
            $.ajax(
                {
                    data: archivo,
                    url:  'Factura/crear_factura.php',
                    type: 'post',
                    beforeSend: function() {
                        $("#actualizar"+rowId).html("<img src=\"img/loader1.gif\"/> Creando Factura");
                        //actualizar();
                    },
                    success: function(response){
                        console.log(response);
                        $("#actualizar"+rowId).html("Factura Creada");
                        actualizar();
                        
                    }
                }
            );       
            
        }        
    });
}

function Up(filecer,rfc,filekey,fcn,fkn,pass,pass2,fi)
{
    console.log("subiendo");
        //se adjunta archivo cer
        var formData = new FormData();
        var fc = filecer;
        formData.append('file', fc);
        //adjuntamos key
        var formDatak = new FormData();
        var fk = filekey;
        formDatak.append('file',fk);
    //console.log(fc);
        if(filecer != undefined)
        {
            ajaxup(formData,rfc,'cer');
        }

        if(filekey != undefined)
        {
            ajaxup(formData,rfc,'key');
        }

        var parms = 
        {
                "filecer" : fcn,
                "filekey": fkn,
                "pass": pass,
                "pass2": pass2,
                "Fecha":fi
        }
        ajaxwriteparms(parms);
}

function ajaxup(file,rfc,tipo)
{
    $.ajax({
        url: "http://robotran.ddns.net:7004/api/upload?rfc="+rfc+"&tipo="+tipo,
        type: "post",
        dataType: "html",
        data: file,
        cache: false,
        contentType: false,
        processData: false      
        }).done(function (resp) {
    console.log('archivo '+tipo+' subido');
    });
}
function ajaxwriteparms(parms)
{
    $.ajax(
        {
            data: parms,
            url:  'helper/escribir.php',
            type: 'post',
            success: function(response){
                console.log(response);
            }
        }
    );
}