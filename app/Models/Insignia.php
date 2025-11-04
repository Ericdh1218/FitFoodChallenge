<?php
namespace App\Models;

use App\Config\DB;
use PDO;

class Insignia
{
    protected $pdo;

    public function __construct()
    {
        $this->pdo = DB::conn();
    }

    /**
     * Busca todas las insignias que ha ganado un usuario específico.
     * Hace un JOIN con la tabla 'insignias' para obtener los detalles.
     */
    public function findInsigniasByUserId(int $userId): array
    {
        $sql = "SELECT 
                    i.nombre, 
                    i.descripcion, 
                    i.icono_url, 
                    iu.fecha_obtenida
                FROM insignias_usuarios iu
                JOIN insignias i ON iu.insignia_id = i.id
                WHERE iu.user_id = :user_id
                ORDER BY iu.fecha_obtenida DESC"; // Mostrar las más recientes primero
        
        $st = $this->pdo->prepare($sql);
        $st->execute([':user_id' => $userId]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
     public function all(): array {
        $sql = "SELECT * FROM insignias ORDER BY id DESC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array {
        $st = $this->pdo->prepare("SELECT * FROM insignias WHERE id=:id");
        $st->execute([':id'=>$id]);
        $r = $st->fetch(PDO::FETCH_ASSOC);
        return $r ?: null;
    }

    public function findByCodigo(string $codigo): ?array {
        $st = $this->pdo->prepare("SELECT * FROM insignias WHERE codigo=:c");
        $st->execute([':c'=>$codigo]);
        $r = $st->fetch(PDO::FETCH_ASSOC);
        return $r ?: null;
    }

    /** Maneja guardado del archivo y devuelve ruta relativa o null */
    private function handleUpload(?array $file, ?string $oldPath=null): ?string {
        if (!$file || empty($file['name']) || $file['error']!==UPLOAD_ERR_OK) {
            return $oldPath; // mantener actual en update
        }
        $allowed = ['image/png'=>'png', 'image/jpeg'=>'jpg', 'image/webp'=>'webp', 'image/svg+xml'=>'svg'];
        $mime = mime_content_type($file['tmp_name']);
        if (!isset($allowed[$mime])) { return $oldPath; }

        $ext = $allowed[$mime];
        $name = uniqid('badge_', true).'.'.$ext;
        $destDir = BASE_PATH.'/public/img/insignias';
        if (!is_dir($destDir)) mkdir($destDir,0775,true);
        $dest = $destDir.'/'.$name;

        if (move_uploaded_file($file['tmp_name'],$dest)) {
            // opcional: borrar anterior si existía
            if ($oldPath && file_exists(BASE_PATH.'/public/'.$oldPath)) @unlink(BASE_PATH.'/public/'.$oldPath);
            return 'img/insignias/'.$name;
        }
        return $oldPath;
    }

    public function create(array $d, ?array $file=null): bool {
        $ruta = $this->handleUpload($file, null);
        $sql = "INSERT INTO insignias (codigo,nombre,descripcion,icono_url,xp_recompensa)
                VALUES (:codigo,:nombre,:descripcion,:icono_url,:xp)";
        $st = $this->pdo->prepare($sql);
        return $st->execute([
            ':codigo'      => trim($d['codigo']),
            ':nombre'      => trim($d['nombre']),
            ':descripcion' => $d['descripcion'] ?? null,
            ':icono_url'   => $ruta,
            ':xp'          => (int)($d['xp_recompensa'] ?? 0),
        ]);
    }

    public function update(int $id, array $d, ?array $file=null): bool {
        $actual = $this->find($id);
        if (!$actual) return false;

        $ruta = $this->handleUpload($file, $actual['icono_url'] ?? null);
        $sql  = "UPDATE insignias SET
                    codigo=:codigo, nombre=:nombre, descripcion=:descripcion,
                    icono_url=:icono_url, xp_recompensa=:xp
                 WHERE id=:id";
        $st = $this->pdo->prepare($sql);
        return $st->execute([
            ':codigo'      => trim($d['codigo']),
            ':nombre'      => trim($d['nombre']),
            ':descripcion' => $d['descripcion'] ?? null,
            ':icono_url'   => $ruta,
            ':xp'          => (int)($d['xp_recompensa'] ?? 0),
            ':id'          => $id,
        ]);
    }

    public function delete(int $id): bool {
        $item = $this->find($id);
        $st = $this->pdo->prepare("DELETE FROM insignias WHERE id=:id");
        $ok = $st->execute([':id'=>$id]);
        if ($ok && $item && !empty($item['icono_url'])) {
            $abs = BASE_PATH.'/public/'.$item['icono_url'];
            if (is_file($abs)) @unlink($abs);
        }
        return $ok;
    }
}