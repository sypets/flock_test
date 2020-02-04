<?php
/*
 * Uses exclusive and blocking lock with fcntl
 * see man  fcntl
 */

// minimum / maximum seconds to sleep for
$randMin = 1;
$randMax = 10;
$filename = "lock.txt";
$abortOnFlockFail = true;

$count = 0;
$errorCount = 0;


if (!file_exists($filename)){

    print("create file " . $filename . "\n");
    $fp = fopen($filename, "c");
    fwrite($fp, "Write something here\n");
    fflush($fp);
    fclose($fp);

}

while (true) {
   print("in loop ...\n");	

    print("open file " . $filename . "\n");
    $fp = fopen($filename, "r");
    if (!$fp) {
        print("Error opening file " . $filename . "\n");
        exit(1);
    }

    print("acquire exclusive lock (should block if lock in use)\n");
    if (dio_fcntl($fp, F_SETLKW, ['type' => F_WRLCK]) === 0) {
        print("flock succeeded\n");

        $sleepFor = rand($randMin, $randMax);
        print("now sleep $sleepFor\n");
        sleep($sleepFor);

        print("free flock\n");
        flock($fp, LOCK_UN);

        $sleepFor = rand($randMin, $randMax);
        print("now sleep $sleepFor\n");
        sleep($sleepFor);


        $count++;
    } else {
        print("ERROR: dio_fcntl returns != 0: This is an error. It should block, if the exclusive lock was already acquired!\n");
	if ($abortOnFlockFail) {
		print("Aborting  ...\n");
		exit(1);
	}
        $errorCount++;
        $sleepFor = rand($randMin, $randMax);
        print("sleep $sleepFor\n");
        sleep($sleepFor);
    }
    fclose($fp);

    printf("count=$count, errors=$errorCount\n\n");
}



