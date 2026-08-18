<?php
use App\Models\Anggaran;
$data = Anggaran::whereNull('parent_id')->with('children')->get();
echo json_encode($data->toArray());
