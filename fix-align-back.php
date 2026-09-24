<?php
$files = [
    'resources/views/quotation/print.blade.php',
    'resources/views/pindah-toko/barang-keluar/print.blade.php',
    'resources/views/penjualan/surat-jalan.blade.php',
    'resources/views/penjualan/print.blade.php',
    'resources/views/invoice/print.blade.php',
    'resources/views/barang-keluar/surat-jalan.blade.php',
    'resources/views/barang-keluar/print.blade.php',
    'resources/views/po/print.blade.php'
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    
    // Revert the center tag back to a div with text-align: center
    $content = preg_replace(
        '/<center style="margin-top: 15px; margin-bottom: 5px;">\s*<img src="\{\{ \$user_ttd->signature \}\}" height="60" alt="signature" style="margin: 0 auto; display: block;">\s*<\/center>/',
        '<div style="text-align: center; margin-top: 15px; margin-bottom: 5px;">
					<img src="{{ $user_ttd->signature }}" height="60" alt="signature">
				</div>',
        $content
    );
    
    file_put_contents($file, $content);
}
