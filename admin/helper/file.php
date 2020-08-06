<?php

//S_FILES[nombre del archivo][Propiedad de archivo]
class file {

    private $txtC;
    private $txtK;
    private $em;
    private $re;
    private $psw;
    private $fecha;
    private $tDoc;

    function __construct(){}

    public function subir($c,$k)
    {
        
        $txtC = $c;
        $txtK = $k;
           
        $this ->fileCer = $txtC;
        $this ->fileKey = $txtK;
    }

    public function getCert()
    {
        return $this->fileCer;
    }
    public function getKey()
    {
        return $this->fileKey;
    }

    public function setTipoDoc($em , $re)
    {
        $this->chbox_handler1 = $em;
        $this->chbox_handler2 = $re;

        if($em)
        {
            if($re)
            {
                $tDoc = 3;
            }
            else
            {
                $tDoc = 2;
            }
        }
        else if($re)
        {
            $tDoc = 1;
        }
        else
        {
            $tDoc = 0;
        }
        $this->tdoc = $tDoc;
    }

    public function getTipoDoc()
    {
        return $this->tdoc;
    }

    public function setPassword($psw1,$psw2)
    {
        if(! $psw1 == "" && ! $psw2 == "")
        {
            if(! $psw1 == $psw2)
            {
                $error_clave = "No coniciden contraseñas";
            }
            else if(strlen($psw1) < 6){
                $error_clave = "La clave debe tener al menos 6 caracteres";
             }
            else if(strlen($psw1) > 16){
                $error_clave = "La clave no puede tener más de 16 caracteres";
             }
             else
             {
                $error_clave = "";
             }
    
             if($error_clave == "")
             {
                 $psw = $psw1;
                 $this->psw = $psw;
             }
             else
             {
                dol_htmloutput_errors ($error_clave);
             }
        }
    }

    public function getPassword()
    {
        return $this->psw;
    }
    public function setFechaIni($fecha)
    {
        list($dia, $mes, $año) = split('[/.-]', $fecha);
        $this->fecha = $mes . '/' . $dia.'/'.$año ;
    }
    public function getFechaIni()
    {
        return $this->fecha;
    }
}
?>