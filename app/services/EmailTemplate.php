<?php

class EmailTemplate {
    /**
     * Renderiza un correo con estilo moderno/tecnológico.
     * $options opcional: ['accent' => '#2563eb', 'emoji' => '🚀', 'footerNote' => '']
     */
    public static function render($title, $introHtml, $bodyRows = [], $cta = null, $options = []) {
        $year   = date('Y');
        $brand  = 'Polaris';
        // Usar logo local servido desde public/assets/img/logo.png para evitar enlaces rotos en correo
        $logo   = (defined('APP_URL') ? APP_URL : '/') . 'assets/img/logo.png';
        $accent = $options['accent'] ?? '#2563eb';
        $emoji  = $options['emoji']  ?? '✨';
        $muted  = '#6b7280';
        $chipBg = 'linear-gradient(135deg, rgba(37,99,235,.15), rgba(16,185,129,.15))';
        $glow   = '0 10px 30px rgba(37,99,235,.15)';

        $rowsHtml = '';
        foreach ($bodyRows as $row) {
            $label = htmlspecialchars($row['label'] ?? '', ENT_QUOTES, 'UTF-8');
            $value = htmlspecialchars($row['value'] ?? '', ENT_QUOTES, 'UTF-8');
            $rowsHtml .= "<tr>
                <td style=\"padding:12px 0;color:{$muted};font-size:12px;letter-spacing:.2px\">{$label}</td>
                <td style=\"padding:12px 0;color:#0f172a;font-weight:700\">{$value}</td>
            </tr>
            <tr><td colspan=\"2\" style=\"border-bottom:1px solid #e5e7eb\"></td></tr>";
        }

        $ctaHtml = '';
        if ($cta && isset($cta['label'], $cta['url'])) {
            $ctaHtml = "<div style=\"text-align:center;margin-top:24px\">
                <a href=\"" . htmlspecialchars($cta['url'], ENT_QUOTES, 'UTF-8') . "\" style=\"
                    display:inline-block;background:{$accent};color:#fff;padding:12px 22px;border-radius:12px;
                    text-decoration:none;font-weight:700;box-shadow:{$glow};letter-spacing:.2px\">"
                . htmlspecialchars($cta['label'], ENT_QUOTES, 'UTF-8') .
                "</a>
            </div>";
        }

        $footerNote = '';
        if (!empty($options['footerNote'])) {
            $footerNote = '<div style="margin-top:8px">' . htmlspecialchars($options['footerNote'], ENT_QUOTES, 'UTF-8') . '</div>';
        }

        // Cabecera con efecto glass y chip de marca
        $header = "<div style=\"position:relative;padding:18px 22px;border-bottom:1px solid #e5e7eb;\">
            <div style=\"display:flex;align-items:center;gap:12px\">
                <div style=\"background:{$chipBg};backdrop-filter:saturate(160%) blur(2px);padding:8px 10px;border-radius:10px;color:#0f172a;font-weight:800;\">{$brand}</div>
                <div style=\"font-size:18px;font-weight:800;color:#0f172a\">" . $emoji . ' ' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . "</div>
            </div>
        </div>";

        // Intro + tabla
        $content = "<div style=\"padding:20px\">{$introHtml}
            <table style=\"width:100%;border-collapse:collapse;margin-top:12px\">{$rowsHtml}</table>
            {$ctaHtml}
        </div>";

        // Ensamble completo
        // Fondo claro con degradado sutil (con fallback a color sólido)
        $bodyBg = 'background:#eef2ff; background-image:linear-gradient(135deg,#eef2ff 0%, #f8fafc 100%);';
        return "<html><body style=\"margin:0;padding:24px;{$bodyBg}\">"
            . "<div style=\"max-width:680px;margin:0 auto\">"
            . "<div style=\"text-align:center;margin-bottom:14px\"><img src=\"{$logo}\" alt=\"RinoRisk\" style=\"height:34px;filter:drop-shadow(0 2px 6px rgba(0,0,0,.25))\"/></div>"
            . "<div style=\"border-radius:16px;overflow:hidden;box-shadow:0 20px 60px rgba(0,10,40,.35);background:#fff;\">"
            . $header
            . $content
            . "<div style=\"padding:14px 20px;color:{$muted};font-size:12px;border-top:1px solid #e5e7eb;background:#fafafa\">"
                . "© {$year} RinoRisk • Sistema Polaris"
                . $footerNote
              . "</div>"
            . "</div>"
            . "<div style=\"text-align:center;color:#94a3b8;font-size:11px;margin-top:10px\">Enviado de forma segura • «Rendimiento sin fricción» ⚡</div>"
            . "</div>"
            . "</body></html>";
    }
}


