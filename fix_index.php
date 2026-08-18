<?php
$file = 'resources/js/Pages/Anggaran/Index.jsx';
$content = file_get_contents($file);

// Replace CheckCircleIcon with ChevronRightIcon and ChevronDownIcon
$content = str_replace(
    "import { Head", 
    "import { ChevronRightIcon, ChevronDownIcon, Head", 
    $content
);

// We need to replace the expansion toggle icon logic:
$oldToggle = <<<EOT
                                                          <button 
                                                              onClick={() => toggleRow(parent.id)}
                                                              className="text-blue-600 hover:text-blue-800 focus:outline-none"
                                                          >
                                                              {expandedRows[parent.id] ? (
                                                                  <MinusCircleIcon className="w-5 h-5" />
                                                              ) : (
                                                                  <CheckCircleIcon className="w-5 h-5 text-blue-500 bg-white rounded-full border-none" />
                                                              )}
                                                          </button>
EOT;

$newToggle = <<<EOT
                                                          <button 
                                                              onClick={() => toggleRow(parent.id)}
                                                              className="text-blue-600 hover:text-blue-800 focus:outline-none"
                                                          >
                                                              {expandedRows[parent.id] ? (
                                                                  <ChevronDownIcon className="w-5 h-5 text-blue-600" />
                                                              ) : (
                                                                  <ChevronRightIcon className="w-5 h-5 text-gray-400 hover:text-blue-500" />
                                                              )}
                                                          </button>
EOT;

$content = str_replace($oldToggle, $newToggle, $content);

// Update handleSubmit to auto-expand
$oldSubmit = <<<EOT
    const handleSubmit = (e) => {
        e.preventDefault();
        if (modalMode === 'add') {
            post(route('anggaran.store'), {
                onSuccess: () => closeModal(),
            });
        } else {
EOT;

$newSubmit = <<<EOT
    const handleSubmit = (e) => {
        e.preventDefault();
        if (modalMode === 'add') {
            post(route('anggaran.store'), {
                onSuccess: () => {
                    if (data.parent_id) {
                        setExpandedRows(prev => ({ ...prev, [data.parent_id]: true }));
                    }
                    closeModal();
                },
            });
        } else {
EOT;

$content = str_replace($oldSubmit, $newSubmit, $content);

file_put_contents($file, $content);
echo "Fix applied.\n";
?>
