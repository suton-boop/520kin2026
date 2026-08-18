<?php
$file = 'resources/js/Pages/Approvals/Index.jsx';
$content = file_get_contents($file);

$oldButtonLogic = <<<EOT
                                                    {(item.approval_status === 'Pending_Manager' && userRole === 'manager') || 
                                                     (item.approval_status === 'Pending_Admin' && (userRole === 'admin' || userRole === 'super-admin')) ? (
EOT;

$newButtonLogic = <<<EOT
                                                    {(item.approval_status === 'Pending_Manager' && userRole === 'manager') || 
                                                     ((item.approval_status === 'Pending_Admin' || item.approval_status === 'Pending_Manager') && (userRole === 'admin' || userRole === 'super-admin')) ? (
EOT;

$content = str_replace($oldButtonLogic, $newButtonLogic, $content);
file_put_contents($file, $content);
echo "Approvals/Index.jsx updated.\n";
?>
