<?php

namespace App\Console\Commands;

use App\Models\Destination;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportRecommendations extends Command
{
    protected $signature = 'jelajah:impor-rekomendasi {file : Lokasi file rekomendasi_top10.csv}';

    protected $description = 'Impor hasil rekomendasi (TF-IDF + cosine similarity) dari Google Colab ke database';

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

        $header = fgetcsv($handle) ?: [];
        $header = array_map(
            fn ($name) => strtolower(trim((string) preg_replace('/^\xEF\xBB\xBF/', '', (string) $name))),
            $header
        );

        $required = ['destination_id', 'similar_destination_id', 'peringkat', 'skor'];
        $missing = array_diff($required, $header);

        if ($missing !== []) {
            fclose($handle);
            $this->error('Kolom wajib tidak ditemukan: '.implode(', ', $missing));
            $this->line('Kolom yang ada: '.implode(', ', $header));

            return self::FAILURE;
        }

        $col = array_flip($header);
        $validIds = array_flip(Destination::query()->pluck('id')->all());
        $now = now();
        $rows = [];
        $seen = [];
        $skipped = 0;

        while (($line = fgetcsv($handle)) !== false) {
            if ($line === [null]) {
                continue; // baris kosong
            }

            $destinationId = (int) ($line[$col['destination_id']] ?? 0);
            $similarId = (int) ($line[$col['similar_destination_id']] ?? 0);
            $rank = (int) ($line[$col['peringkat']] ?? 0);
            $score = $line[$col['skor']] ?? null;
            $key = $destinationId.'-'.$similarId;

            $valid = isset($validIds[$destinationId], $validIds[$similarId])
                && $destinationId !== $similarId
                && $rank >= 1
                && is_numeric($score)
                && ! isset($seen[$key]);

            if (! $valid) {
                $skipped++;

                continue;
            }

            $seen[$key] = true;
            $rows[] = [
                'destination_id' => $destinationId,
                'similar_destination_id' => $similarId,
                'peringkat' => $rank,
                'skor' => round((float) $score, 6),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        fclose($handle);

        if ($rows === []) {
            $this->error('Tidak ada baris yang valid. Data lama tidak diubah.');
            $this->line('Pastikan ID pada CSV sama dengan ID destinasi di database.');

            return self::FAILURE;
        }

        DB::transaction(function () use ($rows) {
            DB::table('destination_similarities')->delete();

            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('destination_similarities')->insert($chunk);
            }
        });

        $covered = count(array_unique(array_column($rows, 'destination_id')));

        $this->info('Impor selesai.');
        $this->line('Pasangan rekomendasi diimpor : '.count($rows));
        $this->line('Destinasi yang punya hasil   : '.$covered.' dari '.count($validIds));

        if ($skipped > 0) {
            $this->warn("{$skipped} baris dilewati (ID tidak ada di database, baris tidak valid, atau duplikat).");
        }

        return self::SUCCESS;
    }
}