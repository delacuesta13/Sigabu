# SIGABU

El **Si**stema de Informaci�n para el proceso de Inscripci�n, Control de Asistencia y **G**esti�n de **A**ctividades de las �reas de Recreaci�n y
Deporte y Art�stica y Cultural del departamento de **B**ienestar **U**niversitario de la [Universidad Cooperativa de Colombia](http://ucc.edu.co/), 
seccional [Cali](http://ucc.edu.co/cali/Paginas/UniversidadCooperativadeColombia_Cali.aspx), es un proyecto de desarrollo de software llevado a cabo 
por Jhon Adri�n Cer�n Guzm�n, aka ***De_la_Cuesta_13***, estudiante del programa de Ingenier�a de Sistemas.

*Sigabu* permite la gesti�n de actividades *(por ej: F�tbol, Voleibol, Danzas, etc.)* -y su clasificaci�n- de las diferentes �reas 
*(por ej: Recreaci�n y Deportes, Art�stica y Cultural)* del departamento de Bienestar U, donde adem�s se puede programar para un determinado periodo
acad�mico *(por ej: 2011-2, segundo semestre de 2011)* las actividades que se ofertar�n, asign�ndoles un horario (e indicando el lugar o espacio
deportivo y/o cultural donde se desarrollar� �ste). Despu�s de programadas las actividades, se permite la gesti�n de las inscripciones<sup>1</sup> de las
personas<sup>2</sup> beneficiarias de Bienestar U en �stas. Por �ltimo, y despu�s de la gesti�n de inscripciones, el Sistema permite el control de 
asistencia de las actividades programadas. 

---

## Requerimientos


* PHP 5.3.5 o superior.
* MySQL 5.1.54 o superior.
* Apache 2.2.17 o superior.

---

## Instalaci�n

Una vez instaladas las tecnolog�as necesarias del Sistema en el servidor:

1. Ubicar el directorio ***sigabu*** en el directorio web ra�z del servidor<sup>3</sup>.
2. Importar el fichero `db/sigabu-db.sql`, el cual contiene el script que genera la BD. 

**Nota:** Aseg�rese que est� habilitado `mod_rewrite` en Apache. 

---

### Configuraci�n

Despu�s de realizar los pasos indicados de la instalaci�n, configure el Sistema de Informaci�n seg�n la configuraci�n de su servidor.
Para configurar el Sistema, s�lo tiene que editar el fichero `config/config.php`.

A continuaci�n se explica las variables de configuraci�n del proyecto, sus posibles valores y su significado dentro del mismo.

* DEVELOPMENT\_ENVIRONMENT
	* tipo: `boolean`.
	* valores: `true | false`
	* explicaci�n: defina como `true` si usar� el Sistema de Informaci�n en ambiente de desarrollo. Ello significa que se notificar�n
	todos los errores encontrados en compilaci�n. En caso de definir como `false`, los errores no ser�n notificados, sino que se guardar�n
	en un log de errores, en el fichero `tmp/logs/error.log`.
* DB\_NAME
	* tipo: `string`.
	* explicaci�n: nombre de la Base de Datos. El sistema viene pre-definido para trabajar con el nombre `sigabu` (tal nombre est� definido en el fichero
	que contiene el script que genera la BD). S�lo cambie el nombre de la BD, si sabe qu� es lo que hace.
* DB\_HOST, DB\_USER, DB\_PASSWORD
	* tipo: `string`.
	* explicaci�n: nombre del host, de usuario y password para establecer conexi�n con MySQL. 	
* BASE\_PATH
	* tipo: `string`.
	* explicaci�n: URL que apunta al directorio `sigabu`.
* PAGINATE\_LIMIT:
	* tipo: `int`.
	* explicaci�n: n�mero l�mite de registros que se mostrar�n al paginar.
* INSCRIPCIONES\_CRUCEHRS:
	* tipo: `boolean`.
	* valores: `true | false`.
	* explicaci�n: en el fichero `config/config.php` est� comentada la explicaci�n de esta variable.

---

1. Es pre-requisito de inscripci�n que la persona tenga un perfil en el periodo de la programaci�n de la actividad. Un perfil es la clasificaci�n
de la persona dentro de la *comunidad universitaria*.  
2. Las personas beneficiarias de Bienestar U, son quienes componen la denominada *comunidad universitaria*. Esta comunidad clasifica las personas
en: estudiantes, docentes (catedr�tico, medio tiempo o tiempo completo), funcionarios, egresados y familiares (del primer grando de consanguineidad o
afinidad de los anteriores).
3. Por lo general (y sin ser una regla), el directorio ra�z de un servidor web es ***www*** o ***htdocs***.