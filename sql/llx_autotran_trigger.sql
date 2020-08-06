-- Copyright (C) 2018	   Gabriel R. Carpio Meneses <gabriel@quantumbit.mx>

CREATE TRIGGER `llx_autotran_trigger` BEFORE INSERT ON `llx_autotran_facturas`
 FOR EACH ROW IF NEW.FolioFiscal IN (SELECT FolioFiscal FROM llx_autotran_facturas) THEN DELETE FROM llx_autotran_facturas WHERE FolioFiscal = NEW.FolioFiscal;
END IF