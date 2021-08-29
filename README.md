## Repositorio Sistema de Gestión Documental

### 1. Estructura
La estructura de carpetas del proyecto es la siguiente:
  1. /configs >> Dockerfiles y archivos de configuración para imagenes y contenedores de base de datos, front-end, proxy y microservicios 
  2. /data >> Datos generados por base de datos y archivos subidos por usuarios
  3. /src >> código fuente de front-end y microservicios

### 2. Clonar repositorio
Para clonar el repositorio por HHTP ejecutar en el ambiente donde se requiera:
```bash
$git clone https://tide.codebasehq.com/plc-sgd-2021/sgd.git
``` 
Para clonar el repositorio por SSH ejecutar en el ambiente donde se requiera:
```bash
$git clone git@codebasehq.com:tide/plc-sgd-2021/sgd.git
``` 

### 3. Crear archivos de configuración
Una vez clonado el repositorio se debe crear manualmente el archivo .env del front-end y el archivo .env de los microservicios debido a que estos archivos no estarán versionados. 
Para realizar esto se debe ejecutar desde la raiz del proyecto:
```bash
$cd configs/fe && cp .env.base .env && cd ../ms && cp .env.base .env && cd ../..
``` 
Cualquier modificacion sobre estos archivos .env debe ser realizada manualmente en todos los ambientes donde se requiera reflejar los ajustes. 


### 4. Crear directorios para volumenes de datos de BD y Archivos 
Una vez creados los archivos de configuracion se debe crear manualmente la estructura de directorios utilizados como volumenes de datos para BD y archivos. 
Para realizar esto se debe ejecutar desde la raiz del proyecto:
```bash
$cd data && mkdir bd && mkdir files && cd bd && mkdir data && mkdir log && mkdir scripts && cd ../..
``` 


### 5. Crear, levantar, ver o bajar estructura de contenedores  
Para ejecutar se debe contar con make y docker-compose instalado. 
Para crear y levantar la estructura de contenedores recreando las imagenes ejecutar:
```bash
$make sgd_up_build
``` 
Para levantar la estructura de contenedores usando imagenes creadas anteriormente ejecutar:
```bash
$make sgd_up
``` 
Para ver la lista de contenedores creados ejecutar:
```bash
$make sgd_view
``` 
Para bajar la estructura de contenedores creados ejecutar:
```bash
$make sgd_down
``` 

### 6. Ejecutar migrations y seeders  
Para ejecutar los migrations contra la BD ejecutar:
```bash
$make sgd_migrate
``` 
Para ejecutar los seeders contra la BD ejecutar:
```bash
$make sgd_seed
``` 