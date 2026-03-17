<?php
/**
 * API para obtener artículos desde la base de datos
 * Devuelve JSON para consumir desde JavaScript
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config/database.php';

try {
    $db = getDB();
    
    // Obtener parámetros
    $action = $_GET['action'] ?? 'list';
    $id = $_GET['id'] ?? null;
    $slug = $_GET['slug'] ?? null;
    $categoria = $_GET['categoria'] ?? null;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
    
    switch ($action) {
        case 'get':
            // Obtener un artículo específico por ID o slug
            if ($id) {
                $stmt = $db->prepare("
                    SELECT a.*, c.nombre as categoria, c.icono as categoria_icono,
                           j.nombre as juego, j.imagen_portada as juego_imagen,
                           u.nombre_completo as autor,
                           GROUP_CONCAT(t.nombre) as tags
                    FROM articulos a
                    LEFT JOIN categorias c ON a.categoria_id = c.id
                    LEFT JOIN juegos j ON a.juego_id = j.id
                    LEFT JOIN usuarios u ON a.autor_id = u.id
                    LEFT JOIN articulo_tags at ON a.id = at.articulo_id
                    LEFT JOIN tags t ON at.tag_id = t.id
                    WHERE a.id = ? AND a.publicado = TRUE
                    GROUP BY a.id
                ");
                $stmt->execute([$id]);
            } elseif ($slug) {
                $stmt = $db->prepare("
                    SELECT a.*, c.nombre as categoria, c.icono as categoria_icono,
                           j.nombre as juego, j.imagen_portada as juego_imagen,
                           u.nombre_completo as autor,
                           GROUP_CONCAT(t.nombre) as tags
                    FROM articulos a
                    LEFT JOIN categorias c ON a.categoria_id = c.id
                    LEFT JOIN juegos j ON a.juego_id = j.id
                    LEFT JOIN usuarios u ON a.autor_id = u.id
                    LEFT JOIN articulo_tags at ON a.id = at.articulo_id
                    LEFT JOIN tags t ON at.tag_id = t.id
                    WHERE a.slug = ? AND a.publicado = TRUE
                    GROUP BY a.id
                ");
                $stmt->execute([$slug]);
            } else {
                echo json_encode(['error' => 'ID o slug requerido']);
                exit;
            }
            
            $articulo = $stmt->fetch();
            
            if ($articulo) {
                // Incrementar vistas
                $updateStmt = $db->prepare("UPDATE articulos SET vistas = vistas + 1 WHERE id = ?");
                $updateStmt->execute([$articulo['id']]);
                
                // Convertir tags a array
                $articulo['tags'] = $articulo['tags'] ? explode(',', $articulo['tags']) : [];
                
                echo json_encode($articulo, JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['error' => 'Artículo no encontrado']);
            }
            break;
            
        case 'list':
            // Listar artículos
            $query = "
                SELECT a.id, a.titulo, a.slug, a.extracto, a.imagen_destacada,
                       a.vistas, a.calificacion, a.destacado, a.fecha_publicacion,
                       c.nombre as categoria, c.icono as categoria_icono,
                       j.nombre as juego,
                       u.nombre_completo as autor
                FROM articulos a
                LEFT JOIN categorias c ON a.categoria_id = c.id
                LEFT JOIN juegos j ON a.juego_id = j.id
                LEFT JOIN usuarios u ON a.autor_id = u.id
                WHERE a.publicado = TRUE
            ";
            
            if ($categoria) {
                $query .= " AND c.slug = :categoria";
            }
            
            $query .= " ORDER BY a.fecha_publicacion DESC LIMIT :limit";
            
            $stmt = $db->prepare($query);
            
            if ($categoria) {
                $stmt->bindParam(':categoria', $categoria, PDO::PARAM_STR);
            }
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            
            $stmt->execute();
            $articulos = $stmt->fetchAll();
            
            echo json_encode($articulos, JSON_UNESCAPED_UNICODE);
            break;
            
        case 'destacados':
            // Obtener artículos destacados
            $stmt = $db->prepare("
                SELECT a.id, a.titulo, a.slug, a.extracto, a.imagen_destacada,
                       a.vistas, a.calificacion, a.fecha_publicacion,
                       c.nombre as categoria, c.icono as categoria_icono,
                       u.nombre_completo as autor
                FROM articulos a
                LEFT JOIN categorias c ON a.categoria_id = c.id
                LEFT JOIN usuarios u ON a.autor_id = u.id
                WHERE a.publicado = TRUE AND a.destacado = TRUE
                ORDER BY a.fecha_publicacion DESC
                LIMIT :limit
            ");
            
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $articulos = $stmt->fetchAll();
            
            echo json_encode($articulos, JSON_UNESCAPED_UNICODE);
            break;
            
        case 'relacionados':
            // Obtener artículos relacionados por categoría o juego
            $articulo_id = $_GET['articulo_id'] ?? null;
            
            if (!$articulo_id) {
                echo json_encode(['error' => 'articulo_id requerido']);
                exit;
            }
            
            $stmt = $db->prepare("
                SELECT a2.id, a2.titulo, a2.slug, a2.extracto, a2.imagen_destacada,
                       a2.fecha_publicacion, c.nombre as categoria, c.icono as categoria_icono
                FROM articulos a1
                JOIN articulos a2 ON (a1.categoria_id = a2.categoria_id OR a1.juego_id = a2.juego_id)
                LEFT JOIN categorias c ON a2.categoria_id = c.id
                WHERE a1.id = ? AND a2.id != ? AND a2.publicado = TRUE
                GROUP BY a2.id
                ORDER BY a2.fecha_publicacion DESC
                LIMIT 3
            ");
            
            $stmt->execute([$articulo_id, $articulo_id]);
            $relacionados = $stmt->fetchAll();
            
            echo json_encode($relacionados, JSON_UNESCAPED_UNICODE);
            break;
            
        default:
            echo json_encode(['error' => 'Acción no válida']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
}