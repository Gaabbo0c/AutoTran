<?php
class conexionex
{
    private $link;

    function __construct()
    {
        
    }

    public function consultarTabla($qry)
    {
        $link = $link=mysqli_connect("robotran.ddns.net:7003","autotran","QX8Mze9iEYJX","autotran");

        if (!$link){
            die('ERROR DE CONEXION CON MYSQL: ' . mysqli_connect_error());
        }

        $result = mysqli_query($link,$qry);

        $res = mysqli_fetch_assoc($result);

        mysqli_close($link);

        return $res;
    }

    public function escribirTabla($qry)
    {
        $link = $link=mysqli_connect("robotran.ddns.net:7003","autotran","QX8Mze9iEYJX","autotran");
        $tab = array();
        if (!$link){
            die('ERROR DE CONEXION CON MYSQL: ' . mysqli_connect_error());
        }
      
        $result = mysqli_query($link,$qry);
        $i = 0;
        while($res = mysqli_fetch_assoc($result))
        {
        $tab[$i] = $res;
        $i++;
        }
        mysqli_close($link);
        return $tab;
       
        
    }

    public function ejecutarsp($qry)
    {
        $link = $link=mysqli_connect("robotran.ddns.net:7003","autotran","QX8Mze9iEYJX","autotran");
        if (!$link){
            die('ERROR DE CONEXION CON MYSQL: ' . mysqli_connect_error());
        }
        if (!$link->query($qry)) {
            echo "Falló CALL: (" . $link->errno . ") " . $link->error;
        }
    }
}
?>