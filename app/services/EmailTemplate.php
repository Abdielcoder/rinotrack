<?php

class EmailTemplate {
    public static function render($title, $introHtml, $bodyRows = [], $cta = null) {
        $year = date('Y');
        $brand = 'RinoTrack';
        $logo = 'https://rinorisk.com/wp-content/uploads/2023/05/rinorisk_logo.png';
        $primary = '#1e40af';
        $accent = '#2563eb';
        $muted = '#6b7280';
        $lines = '';
        foreach ($bodyRows as $row) {
            $label = htmlspecialchars($row['label'] ?? '', ENT_QUOTES, 'UTF-8');
            $value = htmlspecialchars($row['value'] ?? '', ENT_QUOTES, 'UTF-8');
            $lines .= "<tr><td style=\"padding:10px 0;color:{$muted};font-size:12px;\">{$label}</td><td style=\"padding:10px 0;color:#111827;font-weight:600;\">{$value}</td></tr>";
            $lines .= "<tr><td colspan=\"2\" style=\"border-bottom:1px solid #e5e7eb\"></td></tr>";
        }
        $ctaHtml = '';
        if ($cta && isset($cta['label'], $cta['url'])) {
            $ctaHtml = "<div style=\"text-align:center;margin-top:22px\"><a href=\"" . htmlspecialchars($cta['url'], ENT_QUOTES, 'UTF-8') . "\" style=\"display:inline-block;background:{$accent};color:#fff;padding:12px 20px;border-radius:10px;text-decoration:none;font-weight:600\">" . htmlspecialchars($cta['label'], ENT_QUOTES, 'UTF-8') . "</a></div>";
        }
        return "<html><body style=\"background:#f3f4f6;margin:0;padding:24px;font-family:Inter,Arial,sans-serif\">"
            . "<div style=\"max-width:640px;margin:0 auto\">"
            . "<div style=\"text-align:center;margin-bottom:16px\"><img src=\"{$logo}\" alt=\"RinoRisk\" style=\"height:36px\"/></div>"
            . "<div style=\"background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 10px 30px rgba(2,6,23,.08)\">"
            . "<div style=\"border-bottom:3px solid {$accent};padding:16px 20px\"><div style=\"display:flex;align-items:center;gap:10px\"><div style=\"background:{$accent};color:#fff;border-radius:10px;padding:8px 10px;display:inline-flex;align-items:center\"><span style=\"font-size:12px;font-weight:700\">{$brand}</span></div><div style=\"color:#111827;font-size:16px;font-weight:700\">" . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . "</div></div></div>"
            . "<div style=\"padding:20px\">{$introHtml}<table style=\"width:100%;border-collapse:collapse;margin-top:10px\">{$lines}</table>{$ctaHtml}</div>"
            . "<div style=\"padding:14px 20px;color:{$muted};font-size:12px;border-top:1px solid #e5e7eb\">© {$year} RinoRisk • Sistema RinoTrack</div>"
            . "</div></div></body></html>";
    }
}


