-- =============================================================================
-- LIMPIEZA DE DOCUMENTOS (entorno de pruebas)
-- Borra enviados / recibidos / despachados / borradores / solicitudes de prueba.
-- NO toca: users, buzones, roles, tipos de documento, plantillas, saldos anuales.
-- =============================================================================

BEGIN;

-- Colas Laravel (si existen)
DO $$ BEGIN
  IF to_regclass('public.jobs') IS NOT NULL THEN EXECUTE 'TRUNCATE TABLE jobs RESTART IDENTITY'; END IF;
  IF to_regclass('public.failed_jobs') IS NOT NULL THEN EXECUTE 'TRUNCATE TABLE failed_jobs RESTART IDENTITY'; END IF;
  IF to_regclass('public.job_batches') IS NOT NULL THEN EXECUTE 'TRUNCATE TABLE job_batches RESTART IDENTITY'; END IF;
END $$;

-- Módulo solicitudes
DO $$ BEGIN
  IF to_regclass('public.sol_solicitudes') IS NOT NULL THEN
    EXECUTE 'TRUNCATE TABLE sol_solicitudes RESTART IDENTITY CASCADE';
  END IF;
  IF to_regclass('public.sol_dia_administrativo_movimientos') IS NOT NULL THEN
    EXECUTE 'TRUNCATE TABLE sol_dia_administrativo_movimientos RESTART IDENTITY CASCADE';
  END IF;
END $$;

-- Núcleo SGD: documentos y hops de buzón
TRUNCATE TABLE
  documento_buzon_archivo_descarga,
  documento_buzon_notificacion,
  documento_buzon_bitacora,
  documento_buzon_archivo,
  documento_favorito_usuario,
  documento_validacion_registro,
  documento_buzon,
  documento,
  documento_tmp,
  firma_log,
  bloqueo_folio
RESTART IDENTITY CASCADE;

-- Reiniciar identificador de documentos
DO $$ BEGIN
  IF EXISTS (SELECT 1 FROM pg_class WHERE relname = 'documento_identificador_seq') THEN
    PERFORM setval('documento_identificador_seq', 1, false);
  END IF;
END $$;

-- Reiniciar contadores de folio del año actual
UPDATE tipo_documento_buzon_folio
SET valor = 0, updated_at = NOW()
WHERE anio = EXTRACT(YEAR FROM CURRENT_DATE)::int;

COMMIT;
