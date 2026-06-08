<?php
// app/services/PdfExportService.php

class PdfExportService
{
    private const AVATAR_COLORS = [
        '#3B82F6', '#8B5CF6', '#EC4899', '#10B981',
        '#F59E0B', '#EF4444', '#06B6D4', '#6366F1',
    ];

    private const MIME_EXT_MAP = [
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'webp' => 'image/webp',
    ];

    public function __construct(private string $publicRoot) {}

    /**
     * Renders contacts to PDF and streams it as a download.
     * Exits after streaming.
     */
    public function stream(array $contacts): never
    {
        require_once __DIR__ . '/../../vendor/autoload.php';

        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $options->set('chroot', realpath(__DIR__ . '/../../'));
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($this->buildHtml($contacts), 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $canvas = $dompdf->getCanvas();
        $canvas->page_text(
            $canvas->get_width() / 2 - 30,
            $canvas->get_height() - 28,
            'Page {PAGE_NUM} / {PAGE_COUNT}',
            null, 8,
            [0.39, 0.45, 0.55]
        );

        $filename = 'contacts_' . date('Ymd_His') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }

    // ── Avatar ────────────────────────────────────────────────────────────

    private function buildAvatar(array $c): string
    {
        $initials = strtoupper(substr($c['prenom'], 0, 1) . substr($c['nom'], 0, 1));

        if ($c['photo'] && extension_loaded('gd')) {
            $html = $this->tryPhotoAvatar($c['photo']);
            if ($html !== null) return $html;
        }

        return $this->initialsAvatar($initials);
    }

    private function tryPhotoAvatar(string $relativePath): ?string
    {
        $imgPath = rtrim($this->publicRoot, '/') . '/' . ltrim($relativePath, '/');
        if (!file_exists($imgPath)) return null;

        $ext  = strtolower(pathinfo($imgPath, PATHINFO_EXTENSION));
        $mime = self::MIME_EXT_MAP[$ext] ?? 'image/jpeg';

        $src = match ($mime) {
            'image/png'  => @imagecreatefrompng($imgPath),
            'image/webp' => @imagecreatefromwebp($imgPath),
            default      => @imagecreatefromjpeg($imgPath),
        };

        if (!$src) return null;

        $thumb = imagecreatetruecolor(80, 80);
        imagecopyresampled($thumb, $src, 0, 0, 0, 0, 80, 80, imagesx($src), imagesy($src));

        ob_start();
        imagejpeg($thumb, null, 85);
        $b64 = base64_encode(ob_get_clean());

        imagedestroy($src);
        imagedestroy($thumb);

        return "<img src='data:image/jpeg;base64,{$b64}'
                     style='width:38px;height:38px;border-radius:4px;
                            border:1px solid #CBD5E1;display:block;' />";
    }

    private function initialsAvatar(string $initials): string
    {
        $bg = self::AVATAR_COLORS[abs(crc32($initials)) % count(self::AVATAR_COLORS)];

        return "<table cellpadding='0' cellspacing='0' border='0'
                       style='margin:0 auto;border-collapse:collapse;'>
          <tr>
            <td style='width:38px;height:38px;background:{$bg};border-radius:4px;
                        text-align:center;vertical-align:middle;
                        color:#fff;font-weight:700;font-size:14px;
                        font-family:DejaVu Sans,sans-serif;'>{$initials}</td>
          </tr>
        </table>";
    }

    // ── HTML builder ──────────────────────────────────────────────────────

    private function buildHtml(array $contacts): string
    {
        $date  = date('d/m/Y à H:i');
        $count = count($contacts);
        $year  = date('Y');
        $rows  = $this->buildRows($contacts);

        return <<<HTML
        <!DOCTYPE html>
        <html lang="fr">
        <head>
        <meta charset="UTF-8">
        <style>
          * { margin:0; padding:0; }
          body { font-family:DejaVu Sans,sans-serif; color:#1E293B; font-size:11px; background:#fff; }
          table { border-collapse:collapse; }
          .full-w { width:100%; }

          .hdr-top  { background:#1D4ED8; padding:24px 30px; }
          .hdr-stripe { background:#3B82F6; height:4px; }
          .hdr-bot  { background:#1E3A8A; padding:9px 30px; }

          .brand-title { color:#fff; font-size:21px; font-weight:700; }
          .brand-sub   { color:#93C5FD; font-size:10px; margin-top:3px; }
          .gen-label   { color:#BFDBFE; font-size:9px; text-align:right; }
          .gen-date    { color:#fff; font-size:12px; font-weight:700; text-align:right; margin-top:3px; }

          .pill       { background:rgba(255,255,255,.14); border:1px solid rgba(255,255,255,.22);
                        color:#fff; font-size:10px; padding:4px 14px; }
          .pill-count { font-size:13px; font-weight:700; }
          .pill-ghost { background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.14);
                        color:#BFDBFE; font-size:9px; padding:4px 14px; }

          .section-bar { padding:14px 0 6px; border-bottom:2px solid #E2E8F0;
                         color:#64748B; font-size:9px; font-weight:700;
                         text-transform:uppercase; letter-spacing:1px; }

          .data-table { width:100%; border-collapse:collapse; }
          .data-table thead td { background:#F1F5F9; padding:9px 10px; font-size:9px;
                                  font-weight:700; color:#64748B; text-transform:uppercase;
                                  letter-spacing:.7px; border-bottom:2px solid #CBD5E1; }
          .data-table thead td.c { text-align:center; }

          .footer-bar  { margin-top:22px; border-top:1px solid #E2E8F0; padding-top:9px; }
          .footer-copy { color:#94A3B8; font-size:9px; }
          .badge-conf  { background:#FEF3C7; color:#92400E; border:1px solid #FDE68A;
                         font-size:9px; font-weight:700; padding:2px 8px; }
        </style>
        </head>
        <body>

        <!-- HEADER -->
        <table class="full-w" cellpadding="0" cellspacing="0">
          <tr>
            <td class="hdr-top">
              <table class="full-w" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="vertical-align:middle;">
                    <table cellpadding="0" cellspacing="0">
                      <tr>
                        <td style="width:50px;height:50px;background:rgba(255,255,255,.16);
                                    border-radius:10px;text-align:center;vertical-align:middle;font-size:24px;">
                          &#128203;
                        </td>
                        <td style="padding-left:14px;vertical-align:middle;">
                          <div class="brand-title">ContactsPro</div>
                          <div class="brand-sub">Annuaire &mdash; Contacts Personnels</div>
                        </td>
                      </tr>
                    </table>
                  </td>
                  <td style="vertical-align:middle;">
                    <div class="gen-label">Généré le</div>
                    <div class="gen-date">{$date}</div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr><td class="hdr-stripe"></td></tr>
          <tr>
            <td class="hdr-bot">
              <table cellpadding="0" cellspacing="0">
                <tr>
                  <td class="pill"><span class="pill-count">{$count}</span> contact(s) au total</td>
                  <td style="padding-left:10px;">
                    <table cellpadding="0" cellspacing="0">
                      <tr><td class="pill-ghost">Document confidentiel</td></tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>

        <!-- SECTION LABEL -->
        <table class="full-w" cellpadding="0" cellspacing="0">
          <tr><td class="section-bar">&#128101;&nbsp; Liste des contacts</td></tr>
        </table>

        <!-- CONTACTS TABLE -->
        <table class="data-table" cellpadding="0" cellspacing="0">
          <colgroup>
            <col style="width:28px;" />
            <col style="width:54px;" />
            <col />
            <col style="width:110px;" />
            <col style="width:160px;" />
            <col style="width:72px;" />
          </colgroup>
          <thead>
            <tr>
              <td class="c">#</td>
              <td class="c">Photo</td>
              <td>Nom &amp; Prénom</td>
              <td>Téléphone</td>
              <td>Email</td>
              <td class="c">Ajouté le</td>
            </tr>
          </thead>
          <tbody>{$rows}</tbody>
        </table>

        <!-- FOOTER -->
        <table class="full-w footer-bar" cellpadding="0" cellspacing="0">
          <tr>
            <td class="footer-copy">
              &copy; {$year} ContactsPro &nbsp;&bull;&nbsp; Généré automatiquement &nbsp;&bull;&nbsp; Usage interne uniquement
            </td>
            <td style="text-align:right;">
              <table cellpadding="0" cellspacing="0">
                <tr><td class="badge-conf">CONFIDENTIEL</td></tr>
              </table>
            </td>
          </tr>
        </table>

        </body>
        </html>
        HTML;
    }

    private function buildRows(array $contacts): string
    {
        $rows = '';

        foreach ($contacts as $i => $c) {
            $nom     = htmlspecialchars($c['nom'],       ENT_QUOTES, 'UTF-8');
            $prenom  = htmlspecialchars($c['prenom'],    ENT_QUOTES, 'UTF-8');
            $tel     = htmlspecialchars($c['telephone'], ENT_QUOTES, 'UTF-8');
            $email   = htmlspecialchars($c['email'],     ENT_QUOTES, 'UTF-8');
            $dateAjt = isset($c['created_at']) ? date('d/m/Y', strtotime($c['created_at'])) : '—';
            $avatar  = $this->buildAvatar($c);
            $rowBg   = $i % 2 === 0 ? '#FFFFFF' : '#F8FAFC';
            $num     = $i + 1;

            $rows .= "
            <tr>
              <td style='background:{$rowBg};padding:9px 6px;text-align:center;
                          color:#94A3B8;font-size:9px;font-weight:700;
                          border-bottom:1px solid #E2E8F0;'>{$num}</td>
              <td style='background:{$rowBg};padding:7px 8px;text-align:center;
                          border-bottom:1px solid #E2E8F0;'>{$avatar}</td>
              <td style='background:{$rowBg};padding:9px 12px;border-bottom:1px solid #E2E8F0;'>
                <span style='font-weight:700;font-size:11px;color:#0F172A;'>{$nom}</span>
                <span style='font-size:11px;color:#475569;'> {$prenom}</span>
              </td>
              <td style='background:{$rowBg};padding:9px 12px;font-size:10px;
                          color:#475569;border-bottom:1px solid #E2E8F0;'>{$tel}</td>
              <td style='background:{$rowBg};padding:9px 12px;font-size:10px;
                          color:#2563EB;border-bottom:1px solid #E2E8F0;'>{$email}</td>
              <td style='background:{$rowBg};padding:9px 10px;font-size:9px;
                          color:#94A3B8;border-bottom:1px solid #E2E8F0;
                          text-align:center;'>{$dateAjt}</td>
            </tr>";
        }

        return $rows;
    }
}