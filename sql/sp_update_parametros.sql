DELIMITER $$
CREATE  PROCEDURE `sp_update_parametros`(IN `rfc` VARCHAR(13), IN `Nombre` VARCHAR(200), IN `FechaInicio` DATE, IN `psw` VARCHAR(50), IN `tipo` TINYINT)
BEGIN
IF rfc IN (SELECT organizacionRFC FROM llx_autoTran_parametros) THEN 
    	UPDATE llx_autotran_parametros 
         	SET organizacionNombre = Nombre ,
         		FechaInicioDescarga = FechaInicio,
         		satPassword = psw,
         		tipoDocumento = tipo
             WHERE organizacionRFC = rfc;
   END IF;
END$$

DELIMITER ;
