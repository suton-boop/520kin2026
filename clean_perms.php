<?php
use Spatie\Permission\Models\Permission;

$deleted = Permission::whereIn('name', [
    'event-manage', 'participant-manage', 'template-manage', 
    'certificate-generate', 'certificate-send', 'certificate-approve', 
    'tte-manage', 'monitoring-read', 'audit-read'
])->delete();

echo "Deleted " . $deleted . " unused permissions.\n";
