# SIGABU

El **Si**stema de Información para el proceso de Inscripción, Control de Asistencia y **G**estión de **A**ctividades de las áreas de Recreación y
Deporte y Artística y Cultural del departamento de **B**ienestar **U**niversitario de la [Universidad Cooperativa de Colombia](http://ucc.edu.co/), 
seccional [Cali](http://ucc.edu.co/cali/Paginas/UniversidadCooperativadeColombia_Cali.aspx), es un proyecto de desarrollo de software llevado a cabo 
por Jhon Adrián Cerón Guzmán, aka ***De_la_Cuesta_13***, estudiante del programa de Ingeniería de Sistemas.

*Sigabu* permite la gestión de actividades *(por ej: Fútbol, Voleibol, Danzas, etc.)* -y su clasificación- de las diferentes áreas 
*(por ej: Recreación y Deportes, Artística y Cultural)* del departamento de Bienestar U, donde además se puede programar para un determinado periodo
académico *(por ej: 2011-2, segundo semestre de 2011)* las actividades que se ofertarán, asignándoles un horario (e indicando el lugar o espacio
deportivo y/o cultural donde se desarrollará éste). Después de programadas las actividades, se permite la gestión de las inscripciones<sup>1</sup> de las
personas<sup>2</sup> beneficiarias de Bienestar U en éstas. Por último, y después de la gestión de inscripciones, el Sistema permite el control de 
asistencia de las actividades programadas. 

---

## Requerimientos


* PHP 5.3.5 o superior.
* MySQL 5.1.54 o superior.
* Apache 2.2.17 o superior.

---

## Instalación

Una vez instaladas las tecnologías necesarias del Sistema en el servidor:

1. Ubicar el directorio ***sigabu*** en el directorio web raíz del servidor<sup>3</sup>.
2. Importar el fichero `db/sigabu-db.sql`, el cual contiene el script que genera la BD. 

**Nota:** Asegúrese que esté habilitado `mod_rewrite` en Apache. 

---

### Configuración

Después de realizar los pasos indicados de la instalación, configure el Sistema de Información según la configuración de su servidor.
Para configurar el Sistema, sólo tiene que editar el fichero `config/config.php`.

A continuación se explica las variables de configuración del proyecto, sus posibles valores y su significado dentro del mismo.

* DEVELOPMENT\_ENVIRONMENT
	* tipo: `boolean`.
	* valores: `true | false`
	* explicación: defina como `true` si usará el Sistema de Información en ambiente de desarrollo. Ello significa que se notificarán
	todos los errores encontrados en compilación. En caso de definir como `false`, los errores no serán notificados, sino que se guardarán
	en un log de errores, en el fichero `tmp/logs/error.log`.
* DB\_NAME
	* tipo: `string`.
	* explicación: nombre de la Base de Datos. El sistema viene pre-definido para trabajar con el nombre `sigabu` (tal nombre está definido en el fichero
	que contiene el script que genera la BD). Sólo cambie el nombre de la BD, si sabe qué es lo que hace.
* DB\_HOST, DB\_USER, DB\_PASSWORD
	* tipo: `string`.
	* explicación: nombre del host, de usuario y password para establecer conexión con MySQL. 	
* BASE\_PATH
	* tipo: `string`.
	* explicación: URL que apunta al directorio `sigabu`.
* PAGINATE\_LIMIT:
	* tipo: `int`.
	* explicación: número límite de registros que se mostrarán al paginar.
* INSCRIPCIONES\_CRUCEHRS:
	* tipo: `boolean`.
	* valores: `true | false`.
	* explicación: en el fichero `config/config.php` está comentada la explicación de esta variable.

---

1. Es pre-requisito de inscripción que la persona tenga un perfil en el periodo de la programación de la actividad. Un perfil es la clasificación
de la persona dentro de la *comunidad universitaria*.  
2. Las personas beneficiarias de Bienestar U, son quienes componen la denominada *comunidad universitaria*. Esta comunidad clasifica las personas
en: estudiantes, docentes (catedrático, medio tiempo o tiempo completo), funcionarios, egresados y familiares (del primer grando de consanguineidad o
afinidad de los anteriores).
3. Por lo general (y sin ser una regla), el directorio raíz de un servidor web es ***www*** o ***htdocs***.