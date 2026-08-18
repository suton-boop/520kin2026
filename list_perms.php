<?php
use Spatie\Permission\Models\Permission;
$perms = Permission::all();
foreach($perms as $p) {
    echo $p->name . "\n";
}
