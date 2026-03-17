<?php
/**
 * API para discusiones
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config/database.php';

try {
    $db = getDB();
    $action = $_GET['action'] ?? $_POST['action'] ?? 'list';
    
    switch ($action) {
        case 'list':
            // Listar discusiones por juego
            $juego_slug = $_GET['juego'] ?? null;
            
            if (!$juego_slug) {
                echo json_encode(['error' => 'Slug de juego requerido']);
                exit;
            }
            
            $stmt = $db->prepare("
                SELECT d.id, d.titulo, d.contenido, d.autor_nombre,
                       d.vistas, d.likes, d.fecha_creacion,
                       (SELECT COUNT(*) FROM comentarios_discusion cd 
                        WHERE cd.discusion_id = d.id AND cd.aprobado = TRUE) as total_comentarios
                FROM discusiones d
                JOIN juegos j ON d.juego_id = j.id
                WHERE j.slug = ? AND d.aprobado = TRUE
                ORDER BY d.fecha_creacion DESC
            ");
            
            $stmt->execute([$juego_slug]);
            $discusiones = $stmt->fetchAll();
            
            echo json_encode($discusiones, JSON_UNESCAPED_UNICODE);
            break;
            
        case 'get':
            // Obtener una discusión específica
            $id = $_GET['id'] ?? null;
            
            if (!$id) {
                echo json_encode(['error' => 'ID requerido']);
                exit;
            }
            
            $stmt = $db->prepare("
                SELECT d.*, j.nombre as juego
                FROM discusiones d
                JOIN juegos j ON d.juego_id = j.id
                WHERE d.id = ? AND d.aprobado = TRUE
            ");
            
            $stmt->execute([$id]);
            $discusion = $stmt->fetch();
            
            if ($discusion) {
                // Incrementar vistas
                $updateStmt = $db->prepare("UPDATE discusiones SET vistas = vistas + 1 WHERE id = ?");
                $updateStmt->execute([$id]);
                
                echo json_encode($discusion, JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['error' => 'Discusión no encontrada']);
            }
            break;
            
        case 'comentarios':
            // Obtener comentarios de una discusión
            $discusion_id = $_GET['discusion_id'] ?? null;
            
            if (!$discusion_id) {
                echo json_encode(['error' => 'discusion_id requerido']);
                exit;
            }
            
            $stmt = $db->prepare("
                SELECT id, autor_nombre, contenido, fecha_creacion
                FROM comentarios_discusion
                WHERE discusion_id = ? AND aprobado = TRUE
                ORDER BY fecha_creacion DESC
            ");
            
            $stmt->execute([$discusion_id]);
            $comentarios = $stmt->fetchAll();
            
            echo json_encode($comentarios, JSON_UNESCAPED_UNICODE);
            break;
            
        case 'nueva':
            // Crear nueva discusión
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['error' => 'Método no permitido']);
                exit;
            }
            
            $titulo = sanitize($_POST['titulo'] ?? '');
            $contenido = sanitize($_POST['contenido'] ?? '');
            $autor_nombre = sanitize($_POST['autor_nombre'] ?? '');
            $juego_slug = $_POST['juego_slug'] ?? '';
            
            if (empty($titulo) || empty($contenido) || empty($autor_nombre) || empty($juego_slug)) {
                echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos']);
                exit;
            }
            
            // Obtener ID del juego
            $stmtJuego = $db->prepare("SELECT id FROM juegos WHERE slug = ?");
            $stmtJuego->execute([$juego_slug]);
            $juego = $stmtJuego->fetch();
            
            if (!$juego) {
                echo json_encode(['success' => false, 'message' => 'Juego no encontrado']);
                exit;
            }
            
            $stmt = $db->prepare("
                INSERT INTO discusiones (titulo, contenido, autor_nombre, juego_id, aprobado)
                VALUES (?, ?, ?, ?, TRUE)
            ");
            
            if ($stmt->execute([$titulo, $contenido, $autor_nombre, $juego['id']])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Discusión creada exitosamente',
                    'id' => $db->lastInsertId()
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al crear discusión']);
            }
            break;
            
        case 'comentar':
            // Agregar comentario a discusión
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['error' => 'Método no permitido']);
                exit;
            }
            
            $discusion_id = $_POST['discusion_id'] ?? null;
            $autor_nombre = sanitize($_POST['autor_nombre'] ?? '');
            $contenido = sanitize($_POST['contenido'] ?? '');
            
            if (!$discusion_id || empty($autor_nombre) || empty($contenido)) {
                echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos']);
                exit;
            }
            
            $stmt = $db->prepare("
                INSERT INTO comentarios_discusion (discusion_id, autor_nombre, contenido, aprobado)
                VALUES (?, ?, ?, TRUE)
            ");
            
            if ($stmt->execute([$discusion_id, $autor_nombre, $contenido])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Comentario agregado exitosamente',
                    'id' => $db->lastInsertId()
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al agregar comentario']);
            }
            break;
            
        case 'like':
            // Dar like a una discusión
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['error' => 'Método no permitido']);
                exit;
            }
            
            $discusion_id = $_POST['discusion_id'] ?? null;
            
            if (!$discusion_id) {
                echo json_encode(['success' => false, 'message' => 'discusion_id requerido']);
                exit;
            }
            
            $stmt = $db->prepare("UPDATE discusiones SET likes = likes + 1 WHERE id = ?");
            
            if ($stmt->execute([$discusion_id])) {
                // Obtener nuevo conteo
                $getStmt = $db->prepare("SELECT likes FROM discusiones WHERE id = ?");
                $getStmt->execute([$discusion_id]);
                $result = $getStmt->fetch();
                
                echo json_encode([
                    'success' => true,
                    'likes' => $result['likes']
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al dar like']);
            }
            break;
            
        default:
            echo json_encode(['error' => 'Acción no válida']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
}