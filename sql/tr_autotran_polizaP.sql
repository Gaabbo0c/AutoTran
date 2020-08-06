-- Copyright (C) 2018	   Gabriel R. Carpio Meneses <gabriel@quantumbit.mx>


DELIMITER \\
CREATE TRIGGER `tr_autotran_polizaP` AFTER INSERT ON `llx_paiementfourn_facturefourn`
    FOR EACH ROW BEGIN
        IF (SELECT distinct ffd.multicurrency_total_tva FROM llx_contab_polizasdet AS cpd1
	INNER JOIN llx_contab_polizas AS cp ON cpd1.fk_poliza = cp.rowid
    INNER JOIN llx_facture_fourn as ffd ON ffd.rowid = cp.fk_facture
	WHERE cp.tipo_pol = 'D'
	AND cp.fk_facture = NEW.fk_facturefourn
	ORDER by cpd1.asiento DESC) > 0 
    THEN
    INSERT INTO llx_contab_polizas
(
    entity,
    tipo_pol,
    cons,
    fecha,
    anio,
    mes,
    comentario,
    concepto,
    anombrede,
    numcheque,
    fk_facture,
    ant_ctes,
    societe_type

)
VALUES
(
    (SELECT entity FROM llx_paiementfourn WHERE rowid = NEW.fk_paiementfourn LIMIT 1),
    'E',
    (SELECT IFNULL(
        (SELECT cp.cons FROM llx_contab_polizas AS cp 
        INNER JOIN llx_paiementfourn_facturefourn as pff ON cp.rowid = pff.fk_facturefourn
        INNER JOIN llx_paiementfourn as pf ON pff.fk_paiementfourn = pf.rowid
        WHERE cp.tipo_pol = 'E'
        AND anio = (SELECT YEAR(pf1.tms) FROM llx_paiementfourn as pf1 
                    WHERE  pf1.rowid = NEW.fk_paiementfourn)
        AND mes = (SELECT MONTH(pf1.tms) FROM llx_paiementfourn as pf1 
                    WHERE pf1.rowid = NEW.fk_paiementfourn)
        ORDER BY cp.cons DESC LIMIT 1),0) + 1
    ),
     (SELECT pf1.tms FROM llx_paiementfourn as pf1 
    WHERE  pf1.rowid = NEW.fk_paiementfourn),
    (SELECT YEAR(pf1.tms) FROM llx_paiementfourn as pf1 
    WHERE  pf1.rowid = NEW.fk_paiementfourn),
    (SELECT MONTH(pf1.tms) FROM llx_paiementfourn as pf1 
    WHERE pf1.rowid = NEW.fk_paiementfourn),
    'GENERADO AUTOMATICAMENTE',
    ' ',
    ' ',
    ' ',
    NEW.fk_facturefourn,
    0,
    2);
    INSERT INTO llx_contab_polizasdet
	(
	fk_poliza,
	asiento,
	cuenta,
	debe,
	haber,
	descripcion,
	uuid
	)
	VALUES
	(
	(SELECT rowid FROM llx_contab_polizas WHERE fk_facture = NEW.fk_facturefourn order by rowid DESC LIMIT 1),
	1,
	(SELECT cpd1.cuenta FROM llx_contab_polizasdet AS cpd1
	INNER JOIN llx_contab_polizas AS cp ON cpd1.fk_poliza = cp.rowid
	WHERE cp.tipo_pol = 'D'
	AND cp.fk_facture = NEW.fk_facturefourn
	ORDER by cpd1.asiento DESC LIMIT 1),
	(SELECT cpd1.haber FROM llx_contab_polizasdet AS cpd1
	INNER JOIN llx_contab_polizas AS cp ON cpd1.fk_poliza = cp.rowid
	WHERE cp.tipo_pol = 'D'
	AND cp.fk_facture = NEW.fk_facturefourn
	ORDER by cpd1.asiento DESC LIMIT 1),
	0,
	(SELECT cpd1.descripcion FROM llx_contab_polizasdet AS cpd1
	INNER JOIN llx_contab_polizas AS cp ON cpd1.fk_poliza = cp.rowid
	WHERE cp.tipo_pol = 'D'
	AND cp.fk_facture = NEW.fk_facturefourn
	ORDER by cpd1.asiento DESC LIMIT 1),
	(SELECT cpd1.uuid FROM llx_contab_polizasdet AS cpd1
	INNER JOIN llx_contab_polizas AS cp ON cpd1.fk_poliza = cp.rowid
	WHERE cp.tipo_pol = 'D'
	AND cp.fk_facture = NEW.fk_facturefourn
	ORDER by cpd1.asiento DESC LIMIT 1)
	)
    ,
    (
    (SELECT rowid FROM llx_contab_polizas WHERE fk_facture = NEW.fk_facturefourn order by rowid DESC LIMIT 1),
    2,
    IFNULL((SELECT ccc.cta FROM llx_paiementfourn_facturefourn AS pff 
INNER JOIN llx_paiementfourn AS pf ON pf.rowid = pff.fk_paiementfourn
INNER JOIN llx_bank as b ON b.rowid = pf.fk_bank
INNER JOIN llx_bank_account AS ba ON ba.rowid = b.fk_account
INNER JOIN llx_contab_cat_ctas AS ccc ON ccc.descta = ba.label
WHERE pff.rowid = NEW.rowid),'102.01'),
0,
(SELECT distinct ffd.multicurrency_total_ttc FROM llx_contab_polizasdet AS cpd1
	INNER JOIN llx_contab_polizas AS cp ON cpd1.fk_poliza = cp.rowid
    INNER JOIN llx_facture_fourn as ffd ON ffd.rowid = cp.fk_facture
	WHERE cp.tipo_pol = 'D'
	AND cp.fk_facture = NEW.fk_facturefourn
	ORDER by cpd1.asiento DESC),
    (SELECT ba.label FROM llx_paiementfourn_facturefourn AS pff 
INNER JOIN llx_paiementfourn AS pf ON pf.rowid = pff.fk_paiementfourn
INNER JOIN llx_bank as b ON b.rowid = pf.fk_bank
INNER JOIN llx_bank_account AS ba ON ba.rowid = b.fk_account
WHERE pff.rowid = NEW.rowid),
(SELECT distinct cpd1.uuid FROM llx_contab_polizasdet AS cpd1
	INNER JOIN llx_contab_polizas AS cp ON cpd1.fk_poliza = cp.rowid
    INNER JOIN llx_facture_fourn as ffd ON ffd.rowid = cp.fk_facture
	WHERE cp.tipo_pol = 'D'
	AND cp.fk_facture = NEW.fk_facturefourn
	ORDER by cpd1.asiento DESC)
    ),
    (
    (SELECT rowid FROM llx_contab_polizas WHERE fk_facture = NEW.fk_facturefourn order by rowid DESC LIMIT 1),
    3,
    '118.01',
    (SELECT distinct cpd1.haber - ffd.multicurrency_total_ht FROM llx_contab_polizasdet AS cpd1
	INNER JOIN llx_contab_polizas AS cp ON cpd1.fk_poliza = cp.rowid
    INNER JOIN llx_facture_fourn as ffd ON ffd.rowid = cp.fk_facture
	WHERE cp.tipo_pol = 'D'
	AND cp.fk_facture = NEW.fk_facturefourn
	ORDER by cpd1.asiento DESC LIMIT 1),
    0,
    'IVA',
    (SELECT distinct cpd1.uuid FROM llx_contab_polizasdet AS cpd1
	INNER JOIN llx_contab_polizas AS cp ON cpd1.fk_poliza = cp.rowid
    INNER JOIN llx_facture_fourn as ffd ON ffd.rowid = cp.fk_facture
	WHERE cp.tipo_pol = 'D'
	AND cp.fk_facture = NEW.fk_facturefourn
	ORDER by cpd1.asiento DESC)
    ),
    (
       (SELECT rowid FROM llx_contab_polizas WHERE fk_facture = NEW.fk_facturefourn order by rowid DESC LIMIT 1),
    4,
    '119.01',
    0,
    (SELECT distinct cpd1.haber - ffd.multicurrency_total_ht FROM llx_contab_polizasdet AS cpd1
	INNER JOIN llx_contab_polizas AS cp ON cpd1.fk_poliza = cp.rowid
    INNER JOIN llx_facture_fourn as ffd ON ffd.rowid = cp.fk_facture
	WHERE cp.tipo_pol = 'D'
	AND cp.fk_facture = NEW.fk_facturefourn
	ORDER by cpd1.asiento DESC LIMIT 1),
    'IVA',
    (SELECT distinct cpd1.uuid FROM llx_contab_polizasdet AS cpd1
	INNER JOIN llx_contab_polizas AS cp ON cpd1.fk_poliza = cp.rowid
    INNER JOIN llx_facture_fourn as ffd ON ffd.rowid = cp.fk_facture
	WHERE cp.tipo_pol = 'D'
	AND cp.fk_facture = NEW.fk_facturefourn
	ORDER by cpd1.asiento DESC) 
    );
    ELSE
    INSERT INTO llx_contab_polizas
(
    entity,
    tipo_pol,
    cons,
    fecha,
    anio,
    mes,
    comentario,
    concepto,
    anombrede,
    numcheque,
    fk_facture,
    ant_ctes,
    societe_type

)
VALUES
(
    (SELECT entity FROM llx_paiementfourn WHERE rowid = NEW.fk_paiementfourn LIMIT 1),
    'E',
    (SELECT IFNULL(
        (SELECT cp.cons FROM llx_contab_polizas AS cp 
        INNER JOIN llx_paiementfourn_facturefourn as pff ON cp.rowid = pff.fk_facturefourn
        INNER JOIN llx_paiementfourn as pf ON pff.fk_paiementfourn = pf.rowid
        WHERE cp.tipo_pol = 'E'
        AND anio = (SELECT YEAR(pf1.tms) FROM llx_paiementfourn as pf1 
                    WHERE  pf1.rowid = NEW.fk_paiementfourn)
        AND mes = (SELECT MONTH(pf1.tms) FROM llx_paiementfourn as pf1 
                    WHERE pf1.rowid = NEW.fk_paiementfourn)
        ORDER BY cp.cons DESC LIMIT 1),0) + 1
    ),
     (SELECT pf1.tms FROM llx_paiementfourn as pf1 
    WHERE  pf1.rowid = NEW.fk_paiementfourn),
    (SELECT YEAR(pf1.tms) FROM llx_paiementfourn as pf1 
    WHERE  pf1.rowid = NEW.fk_paiementfourn),
    (SELECT MONTH(pf1.tms) FROM llx_paiementfourn as pf1 
    WHERE pf1.rowid = NEW.fk_paiementfourn),
    'GENERADO AUTOMATICAMENTE',
    ' ',
    ' ',
    ' ',
    NEW.fk_facturefourn,
    0,
    2);
    INSERT INTO llx_contab_polizasdet
	(
	fk_poliza,
	asiento,
	cuenta,
	debe,
	haber,
	descripcion,
	uuid
	)
	VALUES
	(
	(SELECT rowid FROM llx_contab_polizas WHERE fk_facture = NEW.fk_facturefourn order by rowid DESC LIMIT 1),
	1,
	(SELECT cpd1.cuenta FROM llx_contab_polizasdet AS cpd1
	INNER JOIN llx_contab_polizas AS cp ON cpd1.fk_poliza = cp.rowid
	WHERE cp.tipo_pol = 'D'
	AND cp.fk_facture = NEW.fk_facturefourn
	ORDER by cpd1.asiento DESC LIMIT 1),
	(SELECT cpd1.haber FROM llx_contab_polizasdet AS cpd1
	INNER JOIN llx_contab_polizas AS cp ON cpd1.fk_poliza = cp.rowid
	WHERE cp.tipo_pol = 'D'
	AND cp.fk_facture = NEW.fk_facturefourn
	ORDER by cpd1.asiento DESC LIMIT 1),
	0,
	(SELECT cpd1.descripcion FROM llx_contab_polizasdet AS cpd1
	INNER JOIN llx_contab_polizas AS cp ON cpd1.fk_poliza = cp.rowid
	WHERE cp.tipo_pol = 'D'
	AND cp.fk_facture = NEW.fk_facturefourn
	ORDER by cpd1.asiento DESC LIMIT 1),
	(SELECT cpd1.uuid FROM llx_contab_polizasdet AS cpd1
	INNER JOIN llx_contab_polizas AS cp ON cpd1.fk_poliza = cp.rowid
	WHERE cp.tipo_pol = 'D'
	AND cp.fk_facture = NEW.fk_facturefourn
	ORDER by cpd1.asiento DESC LIMIT 1)
	)
    ,
    (
    (SELECT rowid FROM llx_contab_polizas WHERE fk_facture = NEW.fk_facturefourn order by rowid DESC LIMIT 1),
    2,
    IFNULL((SELECT ccc.cta FROM llx_paiementfourn_facturefourn AS pff 
INNER JOIN llx_paiementfourn AS pf ON pf.rowid = pff.fk_paiementfourn
INNER JOIN llx_bank as b ON b.rowid = pf.fk_bank
INNER JOIN llx_bank_account AS ba ON ba.rowid = b.fk_account
INNER JOIN llx_contab_cat_ctas AS ccc ON ccc.descta = ba.label
WHERE pff.rowid = NEW.rowid),'102.01'),
0,
(SELECT distinct ffd.multicurrency_total_ttc FROM llx_contab_polizasdet AS cpd1
	INNER JOIN llx_contab_polizas AS cp ON cpd1.fk_poliza = cp.rowid
    INNER JOIN llx_facture_fourn as ffd ON ffd.rowid = cp.fk_facture
	WHERE cp.tipo_pol = 'D'
	AND cp.fk_facture = NEW.fk_facturefourn
	ORDER by cpd1.asiento DESC),
    (SELECT ba.label FROM llx_paiementfourn_facturefourn AS pff 
INNER JOIN llx_paiementfourn AS pf ON pf.rowid = pff.fk_paiementfourn
INNER JOIN llx_bank as b ON b.rowid = pf.fk_bank
INNER JOIN llx_bank_account AS ba ON ba.rowid = b.fk_account
WHERE pff.rowid = NEW.rowid),
(SELECT distinct cpd1.uuid FROM llx_contab_polizasdet AS cpd1
	INNER JOIN llx_contab_polizas AS cp ON cpd1.fk_poliza = cp.rowid
    INNER JOIN llx_facture_fourn as ffd ON ffd.rowid = cp.fk_facture
	WHERE cp.tipo_pol = 'D'
	AND cp.fk_facture = NEW.fk_facturefourn
	ORDER by cpd1.asiento DESC)
    );
    END IF;
    END\\
    
    DELIMITER ; 