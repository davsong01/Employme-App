<?php
namespace App\Services;

use Rap2hpoutre\FastExcel\FastExcel;

class ExcelService
{
    public function fastExport(array $data, string $filename)
    {
        return (new FastExcel($data))->download($filename);
    }

    public function export(array $data, string $filename)
    {
        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            protected $data;

            public function __construct(array $data)
            {
                $this->data = $data;
            }

            public function array(): array
            {
                return $this->data;
            }

            public function headings(): array
            {
                return array_keys($this->data[0]);
            }
        }, $filename);
    }

    public static function import($file)
    {
        $sheets = (new FastExcel)->importSheets($file)->toArray();
        $count = count($sheets);
        $temp = [];
        for ($i = 0; $i < $count; $i++) {
            $temp = [...$temp, ...$sheets[$i]];
        }

        return $temp;
    }
}
