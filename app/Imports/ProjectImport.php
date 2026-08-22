<?php
namespace App\Imports;

use App\Models\Project;
use App\Models\GugusMutu;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProjectImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['nama_proyek'])) {
            return null;
        }

        $gugusMutuId = null;
        if (!empty($row['devisi'])) {
            $gm = GugusMutu::where('name', trim($row['devisi']))->first();
            if ($gm) {
                $gugusMutuId = $gm->id;
            }
        }

        return new Project([
            'name' => $row['nama_proyek'],
            'description' => $row['deskripsi'] ?? null,
            'gugus_mutu_id' => $gugusMutuId,
        ]);
    }
}
