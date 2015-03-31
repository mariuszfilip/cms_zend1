<?php
    for ($i = 1; $i <= 5; ++$i) {
        $pid = pcntl_fork();

        if (!$pid) {
            sleep(1);
            print "In child $i\n";
            exit;
        }
    }
?>
