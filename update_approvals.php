<?php
$fileController = 'app/Http/Controllers/ApprovalController.php';
$contentController = file_get_contents($fileController);

$oldControllerCode = <<<EOT
        if (\$user->hasRole(['admin', 'super-admin'])) {
            \$query->whereIn('approval_status', ['Pending_Admin', 'Approved_Admin', 'Rejected_Admin']);
        } elseif (\$user->hasRole('manager')) {
EOT;

$newControllerCode = <<<EOT
        if (\$user->hasRole(['admin', 'super-admin'])) {
            // Admin sees all across all GM
            \$query->whereIn('approval_status', ['Pending_Manager', 'Approved_Manager', 'Rejected_Manager', 'Pending_Admin', 'Approved_Admin', 'Rejected_Admin']);
        } elseif (\$user->hasRole('manager')) {
EOT;

$contentController = str_replace($oldControllerCode, $newControllerCode, $contentController);
file_put_contents($fileController, $contentController);
echo "ApprovalController updated.\n";
?>
