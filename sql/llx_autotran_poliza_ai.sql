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

CREATE TABLE `llx_autotran_poliza_ia` (
  `rowid` int(11) NOT NULL,
  `cuenta` varchar(200) NOT NULL,
  `concepto` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;