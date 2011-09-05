<?php

/*
 * Copyright (c) 2011 Jhon Adri�n Cer�n <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/** Configuration Variables **/

define ('DEVELOPMENT_ENVIRONMENT',true);

define('DB_NAME', 'sigabu');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');
define('DB_HOST', 'localhost');

define('BASE_PATH','http://localhost/sigabu');

define('PAGINATE_LIMIT', '10');

/** Configuraci�n del comportamiento del proyecto **/

/*
 * INSCRIPCIONES_CRUCEHRS
 * tipo: boolean
 * default: false
 * INSCRIPCIONES_CRUCEHRS (o "inscripci�n cruce de horarios"), seg�n su valor,
 * valida si la inscripci�n de una persona en un curso (programaci�n de una 
 * actividad en un determinado periodo) se cruza con los horarios de otras
 * inscripciones (del mismo periodo).
 * ++ Comportamiento (seg�n su valor) ++
 * true: valida que no se crucen los horarios de la inscripci�n a realizar,
 * con las inscripciones existentes. Si se encuentra que hay cruce de horarios,
 * no se permite la nueva inscripci�n.
 * false: no valida los cruces de los horarios.    
 */
define('INSCRIPCIONES_CRUCEHRS', false);