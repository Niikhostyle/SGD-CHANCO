mensaje_inicio="************************ INICIO EJECUCION ************************"
mensaje_fin="************************ FIN EJECUCION ************************"

sgd:
	@echo "=================== Debe especificar comando ==================="

sgd_up_build:
	@echo $(mensaje_inicio)
	@echo "=================== Creando imagenes y levantando contenedores ==================="
	docker-compose up -d --build
	sleep 5
	@echo "=================== Ejecutando setup ==================="
	docker exec sgd_fe /var/www/sgd/setup.sh

	docker exec -it -u 0 sgd_ms_usuarios bash -c 'cd /src ; composer install'
	docker exec -it -u 0 sgd_ms_tipos_documentos bash -c 'cd /src ; composer install'
	docker exec -it -u 0 sgd_ms_buzones bash -c 'cd /src ; composer install'
	docker exec -it -u 0 sgd_ms_parametros bash -c 'cd /src ; composer install'
	docker exec -it -u 0 sgd_ms_documentos bash -c 'cd /src ; composer install'
	docker exec -it -u 0 sgd_ms_archivos bash -c 'cd /src ; composer install'
	docker exec -it -u 0 sgd_ms_bitacora bash -c 'cd /src ; composer install'
	docker exec -it -u 0 sgd_ms_buscador bash -c 'cd /src ; composer install'
	docker exec -it -u 0 sgd_ms_folios bash -c 'cd /src ; composer install'
	docker exec -it -u 0 sgd_ms_firma bash -c 'cd /src ; composer install'
	docker exec -it -u 0 sgd_ms_verifica bash -c 'cd /src ; composer install'
	docker exec -it -u 0 sgd_ms_notificaciones bash -c 'cd /src ; composer install'
	docker exec -it -u 0 sgd_ms_descargas bash -c 'cd /src ; composer install'

	@echo $(mensaje_fin)

sgd_up:
	@echo $(mensaje_inicio)
	@echo "=================== Levantando contenedores ==================="
	docker-compose up -d
	@echo $(mensaje_fin)

sgd_down:
	@echo $(mensaje_inicio)
	@echo "=================== Bajando contenedores ==================="
	docker-compose down
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