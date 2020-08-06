-- Copyright (C) 2018 - 2019 Gabriel R. Carpio Meneses <gabriel@quantumbit.mx>
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


CREATE TABLE `llx_autotran_parametros` (
  `rowid` int(11) NOT NULL,
  `organizacionId` int(11) NOT NULL DEFAULT '0' COMMENT 'Identificador de la Organización',
  `organizacionNombre` varchar(200) NOT NULL COMMENT 'Nombre de la organización',
  `organizacionRFC` varchar(13) NOT NULL COMMENT 'RFC de organización ',
  `FechaUltimaVerificacion` date DEFAULT NULL COMMENT 'Ultima fecha en que se consulto para descarga',
  `FechaInicioDescarga` date NOT NULL COMMENT 'Fecha inicial de descarga de facturas',
  `satPassword` varchar(200) NOT NULL COMMENT 'Contraseña de acceso SAT',
  `tipoDocumento` tinyint(4) NOT NULL COMMENT '0 = niguno , 1 = Recibidos, 2 = Emitidos, 3 = Todos',
  `MsgError` varchar(200) DEFAULT NULL COMMENT 'Mensaje de Error',
  `Ncer` varchar(200) DEFAULT NULL COMMENT 'Nombre del archivo certificado',
  `cer` text,
  `Nkey` varchar(200) DEFAULT NULL COMMENT 'Nombre del archivo key',
  `archivoKey` text,
  `activo` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Variable para marcar si esta activa la autodescarga',
  `FechaUltimaActualizacion` date DEFAULT NULL COMMENT 'Ultima fecha en que se actualizo tabla en dolibarr',
  `entity` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB;
