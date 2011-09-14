# SIGABU

El **Si**stema de Informaci&oacute;n para el proceso de Inscripci&oacute;n, Control de Asistencia y **G**esti&oacute;n de **A**ctividades de las &aacute;reas 
de Recreaci&oacute;n y Deporte y Art&iacute;stica y Cultural del departamento de **B**ienestar **U**niversitario de la 
[Universidad Cooperativa de Colombia](http://ucc.edu.co/), seccional [Cali](http://ucc.edu.co/cali/Paginas/UniversidadCooperativadeColombia_Cali.aspx),
es un proyecto de desarrollo de software llevado a cabo por Jhon Adri&aacute;n Cer&oacute;n Guzm&aacute;n, aka ***De_la_Cuesta_13***, 
estudiante del programa de Ingenier&iacute;a de Sistemas.

*Sigabu* permite la gesti&oacute;n de actividades *(por ej: F&uacute;tbol, Voleibol, Danzas, etc.)* -y su clasificaci&oacute;n- de las diferentes &aacute;reas
*(por ej: Recreaci&oacute;n y Deportes, Art&iacute;stica y Cultural)* del departamento de Bienestar U, donde adem&aacute;s se puede programar para un determinado 
periodo acad&eacute;mico *(por ej: 2011-2, segundo semestre de 2011)* las actividades que se ofertar&aacute;n, asign&aacute;ndoles un horario 
(e indicando el lugar o espacio deportivo y/o cultural donde se desarrollar&aacute; &eacute;ste). Despu&eacute;s de programadas las actividades, se permite
la gesti&oacute;n de las inscripciones <sup>1</sup> de las personas <sup>2</sup> beneficiarias de Bienestar U en &eacute;stas. Por &uacute;ltimo, y 
despu&eacute;s de la gesti&oacute;n de inscripciones, el Sistema permite el control de asistencia de las actividades programadas. 

## Requerimientos

* PHP 5.3.5 o superior.
* MySQL 5.1.54 o superior.
* Apache 2.2.17 o superior.

## Instalaci&oacute;n

Una vez instaladas las tecnolog&iacute;as necesarias del Sistema en el servidor:

1. Ubicar el directorio ***sigabu*** en el directorio web ra&iacute;z del servidor <sup>3</sup>.
2. Importar el fichero `db/sigabu-db.sql`, el cual contiene el script que genera la BD. 

**Nota:** Aseg&uacute;rese que est&eacute; habilitado `mod_rewrite` en Apache. 

## Configuraci&oacute;n

Despu&eacute;s de realizar los pasos indicados de la instalaci&oacute;n, configure el Sistema de Informaci&oacute;n seg&uacute;n la configuraci&oacute;n 
de su servidor. Para configurar el Sistema, s&oacute;lo tiene que editar el fichero `config/config.php`.

A continuaci&oacute;n se explica las variables de configuraci&oacute;n del proyecto, sus posibles valores y su significado dentro del mismo.

* DEVELOPMENT\_ENVIRONMENT
	* tipo: `boolean`.
	* valores: `true | false`
	* explicaci&oacute;n: defina como `true` si usar&aacute; el Sistema de Informaci&oacute;n en ambiente de desarrollo. Ello significa que se 
	notificar&aacute;n todos los errores encontrados en compilaci&oacute;n. En caso de definir como `false`, los errores no ser&aacute;n notificados, 
	sino que se guardar&aacute;n en un log de errores, en el fichero `tmp/logs/error.log`.
* DB\_NAME
	* tipo: `string`.
	* explicaci&oacute;n: nombre de la Base de Datos. El sistema viene pre-definido para trabajar con el nombre `sigabu` 
	(tal nombre est&aacute; definido en el fichero que contiene el script que genera la BD). S&oacute;lo cambie el nombre de la BD, 
	si sabe qu&eacute; es lo que hace.
* DB\_HOST, DB\_USER, DB\_PASSWORD
	* tipo: `string`.
	* explicaci&oacute;n: nombre del host, de usuario y password para establecer conexi&oacute;n con MySQL.
* BASE\_PATH
	* tipo: `string`.
	* explicaci&oacute;n: URL que apunta al directorio `sigabu`.
* PAGINATE\_LIMIT:
	* tipo: `int`.
	* explicaci&oacute;n: n&uacute;mero l&iacute;mite de registros que se mostrar&aacute;n al paginar.
* INSCRIPCIONES\_CRUCEHRS:
	* tipo: `boolean`.
	* valores: `true | false`.
	* explicaci&oacute;n: en el fichero `config/config.php` est&aacute; comentada la explicaci&oacute;n de esta variable.

## Seguimientos a bugs

Si encontraste un bug, por favor crea un tema aquí en GitHub.

[Crear tema!](https://github.com/delacuesta13/Sigabu/issues)

## Contribuir

* Si&eacute;ntete libre de hacer un ***fork*** a este repositorio.
* Envía una solicitud de ***pull***.

## Autor 

Jhon Adri&aacute;n Cer&oacute;n Guzm&aacute;n.

## Copyright y licencia

Copyright &copy; 2011 Jhon Adri&aacute;n Cer&oacute;n Guzm&aacute;n.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

---
1. Es pre-requisito de inscripci&oacute;n que la persona tenga un perfil en el periodo de la programaci&oacute;n de la actividad. Un perfil es la 
clasificaci&oacute;n de la persona dentro de la *comunidad universitaria*.
2. Las personas beneficiarias de Bienestar U, son quienes componen la denominada *comunidad universitaria*. Esta comunidad clasifica las personas
en: estudiantes, docentes (catedr&aacute;tico, medio tiempo o tiempo completo), funcionarios, egresados y familiares (del primer grando de consanguineidad o
afinidad de los anteriores).
3. Por lo general (y sin ser una regla), el directorio ra&iacute;z de un servidor web es ***www*** o ***htdocs***.
