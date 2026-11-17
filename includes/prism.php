<?php
// Prism 1.29.0 (cdnjs) con integridad SRI. Solo se carga en páginas con fragmento de código.

function prism_head(): string
{
    return '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" integrity="sha384-wFjoQjtV1y5jVHbt0p35Ui8aV8GVpEZkyF99OXWqP/eNJDU93D3Ugxkoyh6Y2I4A" crossorigin="anonymous" referrerpolicy="no-referrer">' . "\n";
}

function prism_scripts(string $lenguaje): string
{
    $sri = [
        'prism-core' => 'sha384-MXybTpajaBV0AkcBaCPT4KIvo0FzoCiWXgcihYsw4FUkEz0Pv3JGV6tk2G8vJtDc',
        'prism-clike' => 'sha384-7LHwxHIDSHTBleLmgDWZbC/IMJsfYfFVOihKhvsrxYW4j47YQcRwZja4ToFE3bA8',
        'prism-markup' => 'sha384-HkMr0bZB9kBW4iVtXn6nd35kO/L/dQtkkUBkL9swzTEDMdIe5ExJChVDSnC79aNA',
        'prism-markup-templating' => 'sha384-LMCbF6eKAywctm2tlnguxYpyYDf0/Z3ecx60ugk8J/pydfawrC24sn2gumP8MWBN',
        'prism-php' => 'sha384-+grZ1BKjM0uVvu5qwWIjfNJct1eZpnnIv7QAo1Qva9uEWA584H7dSk7sZSgwbvT7',
        'prism-python' => 'sha384-WJdEkJKrbsqw0evQ4GB6mlsKe5cGTxBOw4KAEIa52ZLB7DDpliGkwdme/HMa5n1m',
        'prism-java' => 'sha384-DioAMZB4yk91W6LuFit5wJDh8c5Ov09f/MBvja94y0PodMqTpTZeBeejqpRUru7D',
        'prism-javascript' => 'sha384-D44bgYYKvaiDh4cOGlj1dbSDpSctn2FSUj118HZGmZEShZcO2v//Q5vvhNy206pp',
        'prism-sql' => 'sha384-/MKWdycCDliku23mP5sYXbZNuXrzgmQO/jsVxwPFn99dVOaXRyKsqDjarqpueGAp',
    ];
    $deps = [
        'php' => ['prism-clike', 'prism-markup', 'prism-markup-templating', 'prism-php'],
        'python' => ['prism-python'],
        'java' => ['prism-clike', 'prism-java'],
        'javascript' => ['prism-clike', 'prism-javascript'],
        'sql' => ['prism-sql'],
    ][$lenguaje] ?? [];

    $html = '';
    foreach (array_merge(['prism-core'], $deps) as $c) {
        $html .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/' . $c . '.min.js" integrity="' . $sri[$c] . '" crossorigin="anonymous" referrerpolicy="no-referrer"></script>' . "\n";
    }
    return $html;
}
