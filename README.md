Numeros
=======

Conversiones de numeros PHP

Clase original de Felix de Jesus Carrillo Celerino (dcreate)
	https://github.com/dcreate/Numeros/blob/master/letras.php


Modificada por Miquel Botanch

Cambios:

 	* He adaptado el código para que muestre dos decimales.

 	* Permite especificar nombres de moneda y de su centésima parte en singular y plural.

 	* Permite de manera opcional substituir un_mil por mil (como en el castellano de España)

 	* Adaptado para que sea directamente compatible con la versión original, por si se actualiza la clase,
 	  NO SEA NECESARIO corregir las llamadas a lHa función ValorEnLetras().  (ya que ahora tiene 5 parámetros en vez de 2)

 	* Añadida variable $anadir_MN_al_final para mostrar o esconder  (en España no se usa este acrónimo)


Modificada por Pedro Oliver

Cambios:

	* Añadida opción para activar o desactivar el uso de idiomas
	* Añadido una opcion para definir el idioma actual
	* Añadida opción para definir la ruta de idiomas
	* Añado funcion llamada l para el uso de idiomas que solo funciona si esta activado el uso de idiomas
	* Sustituidas todas las cadenas de texto con la funcion $this->l para incorporarlas al sistema de traducciones
	* Movidas las opciones relacionadas con idiomas a los archivos de idiomas
