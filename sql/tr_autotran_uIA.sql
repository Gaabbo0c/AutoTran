-- Copyright (C) 2018	   Gabriel R. Carpio Meneses <gabriel@quantumbit.mx>


DELIMITER \\
CREATE TRIGGER `tr_autotran_uIA` BEFORE UPDATE ON `llx_contab_polizasdet`
 FOR EACH ROW BEGIN
 IF NEW.descripcion NOT IN (SELECT concepto FROM llx_autotran_poliza_ia) THEN
		INSERT INTO llx_autotran_poliza_ia (cuenta,concepto)
		VALUES (NEW.cuenta,NEW.descripcion);
        ELSE
         UPDATE llx_autotran_poliza_ia SET cuenta = NEW.cuenta WHERE concepto = NEW.descripcion;
		END IF;
    END\\
    
    DELIMITER ; 