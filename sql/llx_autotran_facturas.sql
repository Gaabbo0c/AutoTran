-- Copyright (C) 2018	   Gabriel R. Carpio Meneses <gabriel@quantumbit.mx>
--
-- This program is free software: you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation, either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program.  If not, see http://www.gnu.org/licenses/.

CREATE TABLE `llx_autotran_facturas` (
  `organizacionId` int(11) NOT NULL COMMENT 'Identificador de la Organización',
  `Concepto` tinyint(1) DEFAULT NULL COMMENT 'true = emitido , fase = emitido',
  `FolioFiscal` varchar(200) NOT NULL,
  `Factura` varchar(50) DEFAULT NULL,
  `Poliza` varchar(50) DEFAULT NULL,
  `RFCemisor` varchar(13) NOT NULL,
  `NombreEmisor` varchar(200) NOT NULL,
  `RFCreceptor` varchar(13) NOT NULL,
  `NombreReceptor` varchar(200) NOT NULL,
  `FechaEmision` date NOT NULL,
  `FechaCertificacion` date NOT NULL,
  `PAC` varchar(50) NOT NULL,
  `Total` tinytext NOT NULL,
  `Efecto` varchar(50) NOT NULL,
  `Estatus de Cancelacion` varchar(200) DEFAULT NULL,
  `Estado` varchar(50) NOT NULL,
  `URL` text,
  `Estatus de Proceso de Cancelacion` varchar(200) DEFAULT NULL,
  `Fecha de Proceso de Cancelacion` varchar(200) DEFAULT NULL,
  `RFCorganizacion` varchar(13) NOT NULL,
  `rowId` int(11) NOT NULL,
  `Entity` int(11) NOT NULL
) ENGINE=InnoDB;