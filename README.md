# SIGABU

El **Si**stema de Informaci&oacute;n para los procesos de Inscripci&oacute;n, Control de Asistencia y **G**esti&oacute;n de **A**ctividades de las &aacute;reas 
de Recreaci&oacute;n y Deporte y Art&iacute;stica y Cultural del departamento de **B**ienestar **U**niversitario de la 
[Universidad Cooperativa de Colombia](http://ucc.edu.co/), sede [Cali](http://ucc.edu.co/cali/Paginas/UniversidadCooperativadeColombia_Cali.aspx),
es un proyecto de desarrollo de software llevado a cabo por Jhon Adri&aacute;n Cer&oacute;n Guzm&aacute;n, aka [*De_la_Cuesta_13*](https://github.com/delacuesta13), 
estudiante del programa de Ingenier&iacute;a de Sistemas.

*Sigabu* permite la gesti&oacute;n de actividades *(por ej: F&uacute;tbol, Voleibol, Danzas, etc.)* -y su clasificaci&oacute;n- de las diferentes &aacute;reas
*(por ej: Recreaci&oacute;n y Deportes, Art&iacute;stica y Cultural)* del departamento de Bienestar U, donde adem&aacute;s se pueden programar para un determinado 
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
3. Configurar el Sistema y usarlo por ***primera vez***.

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

---

### Primer Uso

Despu&eacute;s de instalado y configurado el sistema, ya est&aacute; listo para usarlo.

El software tiene implementado un sistema de autenticaci&oacute;n, a trav&eacute;s del cual, s&oacute;lo los usuarios autorizados
(quienes tienen asignada una cuenta de usuario con un *nombre de usuario* y una *password*) pueden ingresar al Sistema de Informaci&oacute;n
e interactuar con &eacute;ste.

El script `db/sigabu-db.sql` generador de la Base de Datos, trae pre-definida una persona `demo`, y a su vez, se ha asignado una cuenta de usuario
a &eacute;sta.
 
Los siguientes son los datos pre-definidos de la persona `demo`:

	nombres: John
	apellidos: Doe
	identificacion: 1234567

Como se mencion&oacute; anteriormente, a esta persona se le ha asignado una cuenta de usuario para permitir el primer uso del sistema.

	usuario: admin
	password: 12345678

Con el anterior *nombre de usuario* y la *password*, puede ingresar al sistema.

Una vez ha ingresado en el sistema, realizar los siguientes pasos:

1. Haz click en la opci&oacute;n *personas* de la *dashboard* o del men&uacute; superior.

[![Paso 1](http://l4c.me/uploads/sigabu-primer-uso-paso-1-1316020602_full550.png)](http://l4c.me/fullsize/sigabu-primer-uso-paso-1-1316020602.png "Ver imagen")    
2. Haz click en el submen&uacute; `nuevo`.

[![Paso 2](http://l4c.me/uploads/sigabu-primer-uso-paso-2-1316024886_full550.png)](http://l4c.me/fullsize/sigabu-primer-uso-paso-2-1316024886.png "Ver imagen")    
3. Ingresa los datos obligatorios (marcados con \*) para crear una persona. Una vez ingresado los datos para crear la persona, haz click en el bot&oacute;n ***Guardar***.

[![Paso 3](http://l4c.me/uploads/sigabu-primer-uso-paso-3-1316025326_full550.png)](http://l4c.me/fullsize/sigabu-primer-uso-paso-3-1316025326.png "Ver imagen")    
Si ha ingresado los datos correctamente, deber&iacute;as visualizar un mensaje como &eacute;ste:

[![Paso 4](http://l4c.me/uploads/sigabu-primer-uso-paso-4-1316025723_full550.png)](http://l4c.me/fullsize/sigabu-primer-uso-paso-4-1316025723.png "Ver imagen")    
4. Haz click en la opci&oacute;n *Usuarios* del men&uacute; superior.	
5. Haz click en el submen&uacute; `nuevo`.	
6. Ingresa los datos obligatorios (marcados con \*) para crear una cuenta de usuario. En el campo `Identificacion de la Persona`, ingresa el n&uacute;mero de identificaci&oacute;n
de la persona creada anteriormente (ver **paso 3**). En el campo `Rol` (de usuario), selecciona la opci&oacute;n `Jefe de Bienestar Universitario`.
Una vez ingresado los datos, haz click en el bot&oacute;n ***Guardar***.

[![Paso 5](http://l4c.me/uploads/sigabu-primer-uso-paso-5-1316026509_full550.png)](http://l4c.me/fullsize/sigabu-primer-uso-paso-5-1316026509.png "Ver imagen")    
Si ha ingresado los datos correctamente, deber&iacute;as visualizar un mensaje como &eacute;ste:

[![Paso 6](http://l4c.me/uploads/sigabu-primer-uso-paso-6-1316026842_full550.png)](http://l4c.me/fullsize/sigabu-primer-uso-paso-6-1316026842.png "Ver imagen")    
7. Cierra la sesi&oacute;n que iniciaste con el usuario `admin`, haciendo click en la opci&oacute;n superior derecha `Salir`.	
8. Inicia una nueva sesi&oacute;n con los datos de la cuenta de usuario creada anteriormente (ver **paso 6**).	
9. Haz click en la opci&oacute;n *personas* de la *dashboard* o del men&uacute; superior.	
10. Haz click en la opci&oacute;n *eliminar* de la persona `demo` (ver datos pre-definidos de &eacute;sta). A continuaci&oacute;n aparacer&aacute; una ventana, en la cual
se confirma la opci&oacute;n *eliminar* la persona. Haz click en ***S&iacute;***.

[![Paso 7](http://l4c.me/uploads/sigabu-primer-uso-paso-7-1316027806_full550.png)](http://l4c.me/fullsize/sigabu-primer-uso-paso-7-1316027806.png "Ver imagen")

## Recomendaciones

Para un adecuado uso del Sistema de Información, se recomienda que la estaciones de trabajo (o clientes) del sistema cuenten con las siguientes características:

1. Navegador web [**Google Chrome**](http://www.google.com/chrome) 14.0.835.186 o superior.
2. Resoluci&oacute;n de pantalla superior a **1200\*800** pixeles.

## Seguimientos a bugs

Si encontraste un bug, por favor crea un tema aqu&iacute; en GitHub.

[Crear tema!](https://github.com/delacuesta13/Sigabu/issues)

## Contribuir

* Si&eacute;ntete libre de hacer un ***fork*** a este repositorio.
* Env&iacute;a una solicitud de ***pull***.

## Desarrollo del sistema

El desarrollo del Sistema de Informaci&oacute;n se concibi&oacute; bajo el enfoque de separar &eacute;ste en dos partes: **Front-end** y **Back-end**.

**Back-end** es la interfaz del sistema en la cual administrar, por completo, las funcionalidades implementadas y el comportamiento del mismo. Esta interfaz
est&aacute; delimitada para ser usada por el staff de Bienestar Universitario, desde el Jefe del departamento hasta los monitores de las actividades ofertadas
por Bienestar. **Sigabu** es la denominaci&oacute;n que se la ha dado a esta interfaz.

**Front-end** es la interfaz del sistema abierta a toda la *Comunidad Universitaria*, en la cual se muestran las actividades programadas en un determinado per&iacute;odo;
ampliando la informaci&oacute;n para cada actividad, as&iacute; como los horarios que se definieron para &eacute;sta. Adem&aacute;s de consultar las actividades, la *comunidad*
tiene la posibilidad de inscribirse en &eacute;stas. [*Plibu*](https://github.com/delacuesta13/Plibu) es la denominaci&oacute;n que se le ha dado a esta interfaz.

---

### Importante

Por favor **no** iniciar sesi&oacute;n en las interfaces **Sigabu** y **Plibu** al mismo tiempo, usando un mismo *navegador web*. 
Se recomienda inicar sesi&oacute;n en una sola interfaz, y finalizada la misma, iniciar sesi&oacute;n en la otra interfaz.

## Acerca de  

El *Sistema de Informaci&oacute;n para los procesos de Inscripci&oacute;n, Control de Asistencia y Gesti&oacute;n de Actividades de las &aacute;reas de
Recreaci&oacute;n y Deportes y Art&iacute;stica y Cultural del departamento de Bienestar Universitario de la Universidad Cooperativa de Colombia, sede Cali*, 
es un proyecto de desarrollo de *software*, por medio del cual, optar al t&iacute;tulo de **Ingeniero de Sistemas** de la universidad mencionada.

---

### Autor

Jhon Adri&aacute;n Cer&oacute;n Guzm&aacute;n <[jadrian.ceron@gmail.com](mailto:jadrian.ceron@gmail.com)>.

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
en: estudiantes, docentes (catedr&aacute;tico, medio tiempo o tiempo completo), funcionarios, egresados y familiares (del primer grando de consanguinidad o
afinidad de los anteriores).
3. Por lo general (y sin ser una regla), el directorio ra&iacute;z de un servidor web es ***www*** o ***htdocs***.
