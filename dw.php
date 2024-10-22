<?php
// A mappa, amit tömöríteni szeretnél
$folder = '../';
$zipFileName = 'archive.zip';
 
// ZIP objektum létrehozása
$zip = new ZipArchive();
if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    exit("Nem lehet megnyitni a ZIP fájlt");
}

// A mappa tartalmának hozzáadása a ZIP-hez
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($folder),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file) {
    // Csak a fájlokat adjuk hozzá (a mappákat nem)
    if (!$file->isDir()) {
        // Relatív fájlútvonal megszerzése
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($folder) + 1);

        // Fájl hozzáadása a ZIP-hez
        $zip->addFile($filePath, $relativePath);
    }
}

// ZIP fájl lezárása
$zip->close();

// A ZIP fájl letöltése
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="'.basename($zipFileName).'"');
header('Content-Length: ' . filesize($zipFileName));
readfile($zipFileName);

// Az ideiglenes ZIP fájl törlése
unlink($zipFileName);
exit;
?>
