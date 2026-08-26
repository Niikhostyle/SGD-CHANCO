-- =============================================================================
-- LIMPIEZA SOLO SOLICITUDES (arranque / marcha blanca)
-- Borra: solicitudes, bitácora/pasos, documentos SGD nacidos de solicitudes.
-- NO toca: users, buzones, plantillas, tipos, saldos anuales, movimientos RRHH,
--          oficios / documentos SGD que NO vienen de una solicitud.
-- La próxima solicitud queda con id = 1.
-- =============================================================================

BEGIN;

-- 1) Documentos SGD ligados a solicitudes (si existen)
CREATE TEMP TABLE tmp_sol_docs ON COMMIT DROP AS
SELECT DISTINCT id_documento
FROM sol_solicitudes
WHERE id_documento IS NOT NULL;

DELETE FROM documento_buzon_archivo_descarga d
USING documento_buzon db, tmp_sol_docs t
WHERE d.id_documento_buzon = db.id_documento_buzon
  AND db.id_documento = t.id_documento;

DELETE FROM documento_buzon_notificacion n
USING documento_buzon db, tmp_sol_docs t
WHERE n.id_documento_buzon = db.id_documento_buzon
  AND db.id_documento = t.id_documento;

DELETE FROM documento_buzon_bitacora b
USING documento_buzon db, tmp_sol_docs t
WHERE b.id_documento_buzon = db.id_documento_buzon
  AND db.id_documento = t.id_documento;

DELETE FROM documento_buzon_archivo a
USING documento_buzon db, tmp_sol_docs t
WHERE a.id_documento_buzon = db.id_documento_buzon
  AND db.id_documento = t.id_documento;

DELETE FROM documento_favorito_usuario f
USING tmp_sol_docs t
WHERE f.id_documento = t.id_documento;

DELETE FROM documento_validacion_registro v
USING tmp_sol_docs t
WHERE v.id_documento = t.id_documento;

DELETE FROM documento_buzon db
USING tmp_sol_docs t
WHERE db.id_documento = t.id_documento;

DELETE FROM documento d
USING tmp_sol_docs t
WHERE d.id_documento = t.id_documento;

DELETE FROM firma_log fl
USING tmp_sol_docs t
WHERE fl.id_documento = t.id_documento;

-- 2) Tablas del módulo solicitudes (reinicia secuencias → próximo id = 1)
TRUNCATE TABLE sol_solicitudes RESTART IDENTITY CASCADE;

-- Asegurar secuencia de sol_solicitudes en 1 (por si el nombre de seq varía)
DO $$
DECLARE
  seq text;
BEGIN
  SELECT pg_get_serial_sequence('public.sol_solicitudes', 'id') INTO seq;
  IF seq IS NOT NULL THEN
    EXECUTE format('SELECT setval(%L, 1, false)', seq);
  END IF;
END $$;

COMMIT;

-- Verificación
SELECT 'sol_solicitudes' AS tabla, count(*) AS filas FROM sol_solicitudes;
