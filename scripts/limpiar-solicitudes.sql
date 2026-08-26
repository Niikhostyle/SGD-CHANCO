-- =============================================================================
-- LIMPIEZA SOLO SOLICITUDES (arranque / marcha blanca)
-- Borra: solicitudes, bitácora/pasos, documentos SGD nacidos de solicitudes.
-- NO toca: users, buzones, plantillas, tipos, saldos anuales, movimientos RRHH,
--          oficios / documentos SGD que NO vienen de una solicitud.
-- La próxima solicitud queda con id = 1.
-- =============================================================================

\set ON_ERROR_STOP on

BEGIN;

-- Documentos SGD de solicitudes: enlazados en sol_solicitudes + huérfanos por tipo/materia
CREATE TEMP TABLE tmp_sol_docs ON COMMIT DROP AS
SELECT DISTINCT src.id_documento
FROM (
  SELECT s.id_documento
  FROM sol_solicitudes s
  WHERE s.id_documento IS NOT NULL

  UNION

  SELECT d.id_documento
  FROM documento d
  WHERE d.id_tipo_documento IN (
    SELECT std.id_tipo_documento
    FROM sol_tipo_documentos std
    WHERE std.id_tipo_documento IS NOT NULL
  )

  UNION

  SELECT d.id_documento
  FROM documento d
  JOIN tipo_documento td ON td.id_tipo_documento = d.id_tipo_documento
  WHERE td.nombre_corto LIKE 'SOL-%'
     OR td.descripcion LIKE 'Solicitud:%'
     OR d.materia ILIKE 'Solicitud de %'
) src
WHERE src.id_documento IS NOT NULL;

-- Hijos de documento_buzon de esos documentos
CREATE TEMP TABLE tmp_sol_db ON COMMIT DROP AS
SELECT db.id_documento_buzon
FROM documento_buzon db
JOIN tmp_sol_docs t ON t.id_documento = db.id_documento;

-- 1) Descargas de archivos (FK → documento_buzon_archivo, no documento_buzon)
DELETE FROM documento_buzon_archivo_descarga d
USING documento_buzon_archivo a, tmp_sol_db tb
WHERE d.id_documento_buzon_archivo = a.id_documento_buzon_archivo
  AND a.id_documento_buzon = tb.id_documento_buzon;

DELETE FROM documento_buzon_notificacion n
USING tmp_sol_db tb
WHERE n.id_documento_buzon = tb.id_documento_buzon;

DELETE FROM documento_buzon_bitacora b
USING tmp_sol_db tb
WHERE b.id_documento_buzon = tb.id_documento_buzon;

DELETE FROM documento_buzon_archivo a
USING tmp_sol_db tb
WHERE a.id_documento_buzon = tb.id_documento_buzon;

DELETE FROM documento_favorito_usuario f
USING tmp_sol_docs t
WHERE f.id_documento = t.id_documento;

DELETE FROM documento_validacion_registro v
USING tmp_sol_docs t
WHERE v.id_documento = t.id_documento;

DELETE FROM documento_buzon db
USING tmp_sol_docs t
WHERE db.id_documento = t.id_documento;

DELETE FROM firma_log fl
USING tmp_sol_docs t
WHERE fl.id_documento = t.id_documento;

DELETE FROM documento d
USING tmp_sol_docs t
WHERE d.id_documento = t.id_documento;

-- 2) Módulo solicitudes (CASCADE: sol_solicitud_buzon, sol_solicitud_bitacora, etc.)
TRUNCATE TABLE sol_solicitudes RESTART IDENTITY CASCADE;

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
SELECT 'sol_solicitudes' AS tabla, count(*) AS filas FROM sol_solicitudes
UNION ALL
SELECT 'documento (restantes)', count(*) FROM documento
UNION ALL
SELECT 'documento_buzon (restantes)', count(*) FROM documento_buzon;
