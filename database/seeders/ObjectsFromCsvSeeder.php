<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Obj;

class ObjectsFromCsvSeeder extends Seeder
{
    public function run()
    {
        $path = database_path('data/objects.csv');
        if (!file_exists($path)) {
            $this->command->error("CSV not found: {$path}");
            return;
        }

        $contents = file_get_contents($path);
        // remove BOM if present
        $contents = preg_replace('/^\xEF\xBB\xBF/', '', $contents);
        // split into lines
        $lines = preg_split("/\r\n|\n|\r/", $contents);
        if (!$lines || count($lines) < 2) {
            $this->command->error('CSV is empty or has no data lines.');
            return;
        }

        // detect delimiter by checking header line
        $headerLine = array_shift($lines);
        $delimiter = (substr_count($headerLine, ';') > substr_count($headerLine, ',')) ? ';' : ',';
        $header = str_getcsv($headerLine, $delimiter);
        $header = array_map('trim', $header);

        $expectedCols = count($header);
        $imported = 0;
        $skipped = 0;

        foreach ($lines as $line) {
            if (trim($line) === '') continue; // skip empty
            $row = str_getcsv($line, $delimiter);
            // normalize row length
            if (count($row) !== $expectedCols) {
                $this->command->warn('Skipping malformed CSV row (col count mismatch): ' . $line);
                $skipped++;
                continue;
            }
            $data = array_combine($header, $row);
            if (!is_array($data)) {
                $this->command->warn('Skipping malformed CSV row: ' . $line);
                $skipped++;
                continue;
            }
            $name = isset($data['name']) ? trim($data['name']) : null;
            if (empty($name)) {
                $this->command->warn('Skipping row without name: ' . $line);
                $skipped++;
                continue;
            }
            $catalog = isset($data['catalog']) ? trim($data['catalog']) : null;
            Obj::updateOrCreate(
                ['name' => $name, 'catalog' => $catalog],
                [
                    'ra' => $data['ra'] ?? null,
                    'dec' => $data['dec'] ?? null,
                    'type' => $data['type'] ?? null,
                    'description' => $data['description'] ?? null,
                ]
            );
            $imported++;
        }

        $this->command->info("Objects import finished. Imported: {$imported}, Skipped: {$skipped}");
    }
}
