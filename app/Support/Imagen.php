<?php

namespace App\Support;

/**
 * Normalización server-side de imágenes subidas (defensa en profundidad:
 * el navegador ya las optimiza con canvas antes de enviarlas).
 *
 * - Si hay GD disponible: reescala al lado mayor máximo $max y recomprime
 *   (JPEG q82 / PNG / WEBP según el mime original; SVG pasa directo).
 * - Si no hay GD: guarda el archivo tal cual (ya vino normalizado del cliente).
 *
 * Siempre escribe con nombre aleatorio seguro y extensión de la whitelist.
 */
class Imagen
{
    /**
     * Guarda una imagen subida en $destino (carpeta absoluta).
     *
     * @param  \Illuminate\Http\UploadedFile  $archivo
     * @param  string  $destino   Carpeta destino absoluta
     * @param  string  $prefijo   Prefijo del nombre de archivo
     * @param  int     $max       Lado mayor máximo en px (0 = sin límite)
     * @return array{nombre: string, info: ?string, ancho: ?int, alto: ?int}
     *         info = texto tipo "1920x1080 → 1600x900 · 2,1 MB → 240 KB" cuando se achicó
     */
    public static function guardar($archivo, string $destino, string $prefijo, int $max = 1600): array
    {
        if (! is_dir($destino)) {
            mkdir($destino, 0755, true);
        }

        $extOriginal = strtolower($archivo->getClientOriginalExtension() ?: (string) $archivo->guessExtension());
        $mime = (string) $archivo->getMimeType();

        // SVG (logos) pasa directo, sin tocar
        if ($extOriginal === 'svg' || $mime === 'image/svg+xml') {
            $nombre = $prefijo . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.svg';
            $archivo->move($destino, $nombre);

            return ['nombre' => $nombre, 'info' => null, 'ancho' => null, 'alto' => null];
        }

        // Extensión final según mime real (whitelist)
        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'jpg', // gif animado se aplana a jpg en GD; el cliente ya lo pasó a jpg
            default => in_array($extOriginal, ['jpg', 'jpeg', 'png', 'webp'], true) ? ($extOriginal === 'jpeg' ? 'jpg' : $extOriginal) : 'jpg',
        };
        $nombre = $prefijo . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $ruta = $destino . '/' . $nombre;

        $gd = extension_loaded('gd') && function_exists('imagecreatetruecolor');
        $src = null;
        if ($gd) {
            $path = $archivo->getRealPath();
            $src = match ($mime) {
                'image/jpeg' => @imagecreatefromjpeg($path),
                'image/png' => @imagecreatefrompng($path),
                'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : null,
                'image/gif' => function_exists('imagecreatefromgif') ? @imagecreatefromgif($path) : null,
                default => null,
            };
        }

        if ($src) {
            $wOriginal = imagesx($src);
            $hOriginal = imagesy($src);
            $w = $wOriginal;
            $h = $hOriginal;
            $ratio = $max > 0 ? min(1.0, $max / max($w, $h)) : 1.0;

            if ($ratio < 1.0) {
                $nw = max(1, (int) round($w * $ratio));
                $nh = max(1, (int) round($h * $ratio));
                $dst = imagecreatetruecolor($nw, $nh);
                if ($mime === 'image/png') {
                    imagealphablending($dst, false);
                    imagesavealpha($dst, true);
                } else {
                    // fondo blanco para jpeg (saca la transparencia negra)
                    $blanco = imagecolorallocate($dst, 255, 255, 255);
                    imagefill($dst, 0, 0, $blanco);
                }
                imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
                imagedestroy($src);
                $src = $dst;
                $w = $nw;
                $h = $nh;
            }

            $ok = match ($ext) {
                'png' => imagepng($src, $ruta, 6),
                'webp' => function_exists('imagewebp') ? imagewebp($src, $ruta, 82) : imagejpeg($src, $ruta, 82),
                default => imagejpeg($src, $ruta, 82),
            };
            imagedestroy($src);

            if ($ok && file_exists($ruta)) {
                $info = null;
                if ($ratio < 1.0) {
                    $info = sprintf(
                        '%dx%d → %dx%d px · %s → %s',
                        $wOriginal,
                        $hOriginal,
                        $w,
                        $h,
                        self::humano((int) $archivo->getSize()),
                        self::humano((int) filesize($ruta))
                    );
                }

                return ['nombre' => $nombre, 'info' => $info, 'ancho' => $w, 'alto' => $h];
            }
            // si GD falló al escribir, caer al fallback de abajo
        }

        // Fallback: guardar el original (el cliente ya lo normalizó con canvas)
        $archivo->move($destino, $nombre);

        return ['nombre' => $nombre, 'info' => null, 'ancho' => null, 'alto' => null];
    }

    /** Formatea bytes a texto humano (2,1 MB / 240 KB). */
    public static function humano(int|float $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1, ',', '.') . ' MB';
        }

        return number_format($bytes / 1024, 0, ',', '.') . ' KB';
    }
}
