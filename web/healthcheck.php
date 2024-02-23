<?php

declare(strict_types=1);

$lastCronExecution = (int)trim(file_get_contents('healthcheck-cron.txt'));

echo (time() - $lastCronExecution < 120 ? 'OK' : 'FAIL') . ', ' . $lastCronExecution;
