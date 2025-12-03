include .env
DBPASSWORD := $(shell tr -dc 'A-Za-z0-9!@#$%^&*()_+{}|:<>?=' < /dev/urandom | head -c 16)

mensaje_inicio="************************ INICIO EJECUCION ************************"
mensaje_fin="************************ FIN EJECUCION ************************"


sgd:
	@echo "=================== Debe especificar comando ==================="
sgd_test:
	sed -i 's/###APP_URL###/$(APP_URL)/g' configs/fe/.env.base
	sed -i 's/###APP_PORT###/$(APP_HTTPS_PORT)/g' configs/fe/.env.base
#	sed -i 's/###DB_PASSWORD###/$(DBPASSWORD)/g' configs/fe/.env.base
sgd_up_build:
	@echo $(mensaje_inicio)
	@echo "=================== Copiando archivos de configuracion ==================="
	test -f configs/fe/.env || cp configs/fe/.env.base configs/fe/.env
	test -f configs/ms/.env || cp configs/ms/.env.base configs/ms/.env

	@echo "=================== Configurando certificado ==================="

	test -f certificados/$(APP_DOMINIO).cert || cp certificados/certificado.cert certificados/$(APP_DOMINIO).cert
	test -f certificados/$(APP_DOMINIO).key || cp certificados/certificado.key certificados/$(APP_DOMINIO).key
	
	@echo "=================== Configurando variables de entorno ==================="
	sed -i 's/###APP_URL###/$(APP_URL)/g' configs/fe/.env
	sed -i 's/###APP_PORT###/$(APP_HTTPS_PORT)/g' configs/fe/.env
#	sed -i 's/###DB_PASSWORD###/$(DBPASSWORD)/g' configs/fe/.env

	sed -i 's/###APP_URL###/$(APP_URL)/g' configs/fe/.env
	sed -i 's/###APP_PORT###/$(APP_HTTPS_PORT)/g' configs/ms/.env
# sed -i 's/###DB_PASSWORD###/$(DBPASSWORD)/g' configs/ms/.env


	@echo "=================== Creando imagenes y levantando contenedores ==================="
	docker compose up -d --build
	sleep 5
	@echo "=================== Ejecutando setup ==================="
	docker exec sgd_fe /var/www/sgd/setup.sh
	docker exec -it -u 0 sgd_ms_usuarios bash -c 'cd /src ; composer install'
	docker exec -it -u 0 sgd_ms_tipos_documentos bash -c 'cd /src ; composer install'
	docker exec -it -u 0 sgd_ms_buzones bash -c 'cd /src ; composer install'
#	docker exec -it -u 0 sgd_ms_parametros bash -c 'cd /src ; composer install'
	docker exec -it -u 0 sgd_ms_documentos bash -c 'cd /src ; composer install'
	docker exec -it -u 0 sgd_ms_archivos bash -c 'cd /src ; composer install'
	docker exec -it -u 0 sgd_ms_bitacora bash -c 'cd /src ; composer install'
	docker exec -it -u 0 sgd_ms_buscador bash -c 'cd /src ; composer install'
	docker exec -it -u 0 sgd_ms_folios bash -c 'cd /src ; composer install'
	docker exec -it -u 0 sgd_ms_firma bash -c 'cd /src ; composer install'
	docker exec -it -u 0 sgd_ms_verifica bash -c 'cd /src ; composer install'
	docker exec -it -u 0 sgd_ms_notificaciones bash -c 'cd /src ; composer install'
	docker exec -it -u 0 sgd_ms_descargas bash -c 'cd /src ; composer install'

#agregar proteccion a carpetas de configuracion
	docker exec -it sgd_fe composer update
	docker exec -it sgd_fe php artisan ziggy:generate
	docker exec -it sgd_fe php artisan optimize
	docker exec -it sgd_fe php artisan view:clear

	docker exec -it sgd_fe npm run dev
	@echo $(mensaje_fin)

sgd_up:
	@echo $(mensaje_inicio)
	@echo "=================== Levantando contenedores ==================="
	docker compose up -d
	@echo $(mensaje_fin)

sgd_down:
	@echo $(mensaje_inicio)
	@echo "=================== Bajando contenedores ==================="
	docker compose down
	@echo $(mensaje_fin)

sgd_view:
	@echo $(mensaje_inicio)
	@echo "=================== Lista contenedores creados ==================="
	docker ps -f name=sgd_*
	@echo $(mensaje_fin)	

sgd_migrate:
	@echo $(mensaje_inicio)
	@echo "=================== Ejecutando migraciones ==================="
	docker exec -it sgd_fe php artisan migrate
	@echo $(mensaje_fin)	

sgd_seed:
	@echo $(mensaje_inicio)
	@echo "=================== Ejecutando seeders ==================="
	docker exec -it sgd_fe php artisan db:seed
	@echo $(mensaje_fin)

sgd_customize:
	@echo $(mensaje_inicio)
	@echo "=================== Personalizando SGD ==================="
	cp configs/customize/logo1.png src/fe/public/img/logo1.png
	cp configs/customize/logo2.png src/fe/public/img/logo2.png
	cp configs/customize/logo3.png src/fe/public/img/logo3.png
	cp configs/customize/logo4.png src/fe/public/img/logo4.png
	cp configs/customize/icono_logo2.png src/fe/public/img/icono_logo2.png
	cp configs/customize/fondo_login.jpg src/fe/public/img/fondo_login.jpg
	cp configs/customize/favicon.ico src/fe/public/favicon.ico
	cp configs/customize/custom.css src/fe/public/css/custom.css
	@echo $(mensaje_fin)
sgd_cert:
	@echo $(mensaje_inicio)
	@echo "=================== Ejecutando Certificado ==================="
	docker compose down
	docker compose run --rm sgd_ms_cert certonly  --webroot --webroot-path /var/www/certbot -d $(APP_URL)
	cp configs/cert/live/$(APP_URL)/privkey.pem certificados/certificado.key
	cp configs/cert/live/$(APP_URL)/fullchain.pem certificados/certificado.cert
	docker compose down
	docker compose up -d
	@echo $(mensaje_fin)