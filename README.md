## Repositorio Sistema de Gestión Documental

### 1. Estructura
La estructura de carpetas del proyecto es la siguiente:
  1. /configs >> Dockerfiles y archivos de configuración para imagenes y contenedores de base de datos, front-end, proxy y microservicios 
  2. /data >> Datos generados por base de datos y archivos subidos por usuarios
  3. /src >> código fuente de front-end y microservicios

### 2. Clonar repositorio
Para clonar el repositorio, debe solicitar clave de repositorio, posteriormente, por SSH ejecutar en el ambiente donde se requiera:
```bash
https://x-token-auth:[APP-TOKEN]@bitbucket.org/padrelascasas/sgd.git
``` 

### 3. Modificar Variable de entorno

Una vez clonado el repositorio se debe modificar manualmente el archivo .env que se encuentra en la carpeta raíz, según las necesidades del proyecto
```bash

APP_HTTP_PORT : puerto http
APP_HTTPS_PORT : puerto https
APP_URL : URL pública del proyecto
APP_DOMINIO : dominio del proyecto

``` 
Cualquier modificación sobre estos archivos .env debe ser realizada antes de ejecutar los siguientes comandos

### 4. Crear, levantar, ver o bajar estructura de contenedores
Para instalar dependencias y configurar el proyecto por primera vez
```bash
$make sgd_up_build
``` 

### 5. Ejecutar migrations, seeders y personalización   
Para ejecutar los migrations contra la BD ejecutar:
```bash
$make sgd_migrate
``` 
Para ejecutar los seeders contra la BD ejecutar:
```bash
$make sgd_seed
```
Para personalizar imágenes y logos (copiar imágenes y contenido en ```configs/customize``` ):
```bash
$make sgd_customize
```

### 6. Crear, levantar, ver o bajar estructura de contenedores  
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
