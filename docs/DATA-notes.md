
This file is used only for some data notes, please read [DEVELOPMENT.md](DEVELOPMENT.md)

## Adjuntos

Pictures, archives etc are saves using base64 strings, as most generic way, 
due this: https://www.sqlite.org/datatype3.html#date_and_time_datatype the
data types will be TEXT for.

## Items

El sistema es solo una especie de **repositorio de la parte inmutable de una 
empresa, sus activos que son los productos y el capital de bienes**, los servicios 
cuando son productos se representan como items tambien.

## Descripciones e idiomas

Para el soporte multi idiomas se emplea https://en.wikipedia.org/wiki/ISO_639-3#Usage

No se guardan caracteristicas que cambian sino las inmutables ejemplo, guarda 
el idioma español pero no el pais donde se habla o al que pertenece.

## Caracteristicas

Los productos pueden cambiar de categoria pero no de caracteristicas, este 
sistema no guarda sino las caracteristicas inmutables, como el tipo de material 
en que esta fabricado o el dia de la primera aquisicion del mismo.

## Diccionario de datos

El diseño para el diccionario de datos esta en [erp-articulosapidb.mwb](erp-articulosapidb.mwb)
y se puede usar el script [erp-articulosapidb.sql](erp-articulosapidb.sql) para 
usar la base de datos. Los comentarios describen en minimo la logica de la 
funcionalidad de los mismos.

**WARNING** : sqlite must use https://qgqlochekone.blogspot.com/2017/03/mysql-to-sqlite-comments-error-near.html

![erp-articulosapidb.png](erp-articulosapidb.png)
