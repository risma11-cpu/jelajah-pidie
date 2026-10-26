<?php

namespace App\Console\Commands;

use App\Models\Destination;
use Illuminate\Console\Command;

class ImportCoordinates extends Command
{
    protected $signature = 'jelajah:impor-koordinat
                            {file : CSV hasil export Excel}
                            {--dry-run : Hanya tampilkan hasil, tidak menyimpan ke database}';

    protected $description = 'Impor koordinat destinasi dari CSV (hasil export Excel) ke tabel destinations';

    // Batas kasar wilayah Aceh, untuk menangkap latitude/longitude yang tertukar atau salah ketik.
    private const LAT_RANGE = [2.0, 6.2];

    private const LNG_RANGE = [94.9, 98.4];

    public function handle(): int
    {
        $file = (string) $this->argument('file');
        $path = is_file($file) ? $file : base_path($file);

        if (! is_file($path)) {
            $this->error("File tidak ditemukan: {$file}");

            return self::FAILURE;
        }

        $handle = fopen($path, 'r');

        if ($handle === false) {
            $this->error('File tidak bisa dibuka.');

            return self::FAILURE;
        }

        // Excel versi Indonesia biasanya menyimpan CSV dengan pemisah titik koma.
        $firstLine = (string) fgets($handle);
        rewind($handle);
        $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';

        $header = array_map(
            fn ($name) => strtolower(trim((string) preg_replace('/^\xEF\xBB\xBF/', '', (string) $name))),
            fgetcsv($handle, 0, $delimiter) ?: []
        );

        // Kolom pengenal: id, atau nama (name / nama destinasi).
        $idCol = array_search('id', $header, true);
        $nameCol = array_search('name', $header, true);
        $nameCol = $nameCol !== false ? $nameCol : array_search('nama destinasi', $header, true);

        // Koordinat: dua kolom (latitude, longitude) atau satu kolom berisi "lat, lng".
        $latCol = array_search('latitude', $header, true);
        $lngCol = array_search('longitude', $header, true);
        $comboCol = false;

        foreach (['kode (diisi manual)', 'koordinat', 'kode'] as $candidate) {
            if (($found = array_search($candidate, $header, true)) !== false) {
                $comboCol = $found;
                break;
            }
        }

        $hasPair = $latCol !== false && $lngCol !== false;

        if (($idCol === false && $nameCol === false) || (! $hasPair && $comboCol === false)) {
            fclose($handle);
            $this->error('Header CSV harus punya kolom nama (Nama Destinasi) atau id, dan koordinat (latitude + longitude, atau satu kolom "lat, lng").');
            $this->line('Kolom yang terbaca: '.implode(', ', $header));

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $updated = 0;
        $problems = [];
        $rowNumber = 1;

        while (($line = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rowNumber++;

            if ($line === [null]) {
                continue; // baris kosong
            }

            $byId = $idCol !== false;
            $identifier = trim((string) ($line[$byId ? $idCol : $nameCol] ?? ''));

            if ($hasPair) {
                $lat = $this->toFloat($line[$latCol] ?? null);
                $lng = $this->toFloat($line[$lngCol] ?? null);
            } else {
                // Contoh isi sel: "5.415606, 95.761469"
                $parts = preg_split('/\s*[,;]\s*/', trim((string) ($line[$comboCol] ?? ''))) ?: [];
                $lat = count($parts) === 2 ? $this->toFloat($parts[0]) : null;
                $lng = count($parts) === 2 ? $this->toFloat($parts[1]) : null;
            }

            if ($identifier === '') {
                continue;
            }

            if ($lat === null || $lng === null) {
                $problems[] = "Baris {$rowNumber} ({$identifier}): koordinat kosong atau formatnya tidak terbaca.";

                continue;
            }

            if ($lat < self::LAT_RANGE[0] || $lat > self::LAT_RANGE[1]
                || $lng < self::LNG_RANGE[0] || $lng > self::LNG_RANGE[1]) {
                $problems[] = "Baris {$rowNumber} ({$identifier}): koordinat {$lat}, {$lng} di luar wilayah Aceh. Cek apakah latitude/longitude tertukar.";

                continue;
            }

            $query = $byId
                ? Destination::query()->whereKey((int) $identifier)
                : Destination::query()->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($identifier)]);

            $matches = $query->get();

            if ($matches->count() !== 1) {
                $problems[] = "Baris {$rowNumber} ({$identifier}): ".($matches->isEmpty() ? 'tidak ditemukan di database.' : 'cocok dengan lebih dari satu destinasi.');

                continue;
            }

            if (! $dryRun) {
                $matches->first()->update([
                    'latitude' => round($lat, 7),
                    'longitude' => round($lng, 7),
                ]);
            }

            $updated++;
        }

        fclose($handle);

        $this->info($dryRun ? 'Dry run selesai (tidak ada data yang disimpan).' : 'Impor koordinat selesai.');
        $this->line('Destinasi yang '.($dryRun ? 'akan diperbarui' : 'diperbarui').": {$updated}");

        if ($problems !== []) {
            $this->warn(count($problems).' baris dilewati:');

            foreach ($problems as $problem) {
                $this->line(' - '.$problem);
            }
        }

        return self::SUCCESS;
    }

    /**
     * Terima "5.1234" maupun "5,1234" (format desimal Excel Indonesia).
     */
    private function toFloat(mixed $value): ?float
    {
        $value = str_replace(',', '.', trim((string) $value));

        return is_numeric($value) ? (float) $value : null;
    }
}
