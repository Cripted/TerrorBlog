<?php
/**
 * API para juegos y estadísticas
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config/database.php';

try {
    $db = getDB();
    $action = $_GET['action'] ?? 'list';
    
    switch ($action) {
        case 'list':
            // Listar todos los juegos activos
            $stmt = $db->query("
                SELECT id, nombre, slug, descripcion, desarrollador, 
                       anio_lanzamiento, imagen_portada, imagen_banner, calificacion
                FROM juegos
                WHERE activo = TRUE
                ORDER BY nombre ASC
            ");
            
            $juegos = $stmt->fetchAll();
            echo json_encode($juegos, JSON_UNESCAPED_UNICODE);
            break;
            
        case 'get':
            // Obtener un juego específico
            $slug = $_GET['slug'] ?? null;
            
            if (!$slug) {
                echo json_encode(['error' => 'Slug requerido']);
                exit;
            }
            
            $stmt = $db->prepare("
                SELECT j.*,
                       (SELECT COUNT(*) FROM articulos a 
                        WHERE a.juego_id = j.id AND a.publicado = TRUE) as total_articulos,
                       (SELECT COUNT(*) FROM discusiones d 
                        WHERE d.juego_id = j.id AND d.aprobado = TRUE) as total_discusiones
                FROM juegos j
                WHERE j.slug = ? AND j.activo = TRUE
            ");
            
            $stmt->execute([$slug]);
            $juego = $stmt->fetch();
            
            if ($juego) {
                echo json_encode($juego, JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['error' => 'Juego no encontrado']);
            }
            break;
            
        case 'estadisticas':
            // Obtener estadísticas generales
            $stmt = $db->query("
                SELECT 
                    miembros_activos,
                    articulos_publicados,
                    discusiones_activas,
                    comentarios_totales,
                    fecha_actualizacion
                FROM estadisticas
                WHERE id = 1
            ");
            
            $stats = $stmt->fetch();
            
            if (!$stats) {
                // Si no hay estadísticas, crear valores por defecto
                $stats = [
                    'miembros_activos' => 15847,
                    'articulos_publicados' => 1234,
                    'discusiones_activas' => 8956,
                    'comentarios_totales' => 42387
                ];
            }
            
            echo json_encode($stats, JSON_UNESCAPED_UNICODE);
            break;
            
        case 'top_contribuidores':
            // Obtener top 5 contribuidores
            // Esto es un ejemplo, podrías calcularlo de diferentes formas
            $stmt = $db->query("
                SELECT 
                    autor_nombre as nombre,
                    COUNT(*) as total_discusiones,
                    SUM(likes) as total_likes
                FROM discusiones
                WHERE aprobado = TRUE
                GROUP BY autor_nombre
                ORDER BY total_discusiones DESC, total_likes DESC
                LIMIT 5
            ");
            
            $contribuidores = $stmt->fetchAll();
            
            // Si no hay suficientes, agregar datos de ejemplo
            if (count($contribuidores) < 5) {
                $ejemplos = [
                    ['nombre' => 'HorrorMaster88', 'total_discusiones' => 45, 'total_likes' => 156],
                    ['nombre' => 'SilentFan_2026', 'total_discusiones' => 38, 'total_likes' => 132],
                    ['nombre' => 'PyramidHead_Forever', 'total_discusiones' => 29, 'total_likes' => 118],
                    ['nombre' => 'REVillageExplorer', 'total_discusiones' => 22, 'total_likes' => 95],
                    ['nombre' => 'GhostHunter_Pro', 'total_discusiones' => 19, 'total_likes' => 87]
                ];
                
                $contribuidores = array_merge($contribuidores, $ejemplos);
                $contribuidores = array_slice($contribuidores, 0, 5);
            }
            
            echo json_encode($contribuidores, JSON_UNESCAPED_UNICODE);
            break;
            
        default:
            echo json_encode(['error' => 'Acción no válida']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
}