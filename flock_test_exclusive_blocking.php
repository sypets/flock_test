<?php
/*
 * Uses exclusive and blocking lock
 */

// minimum / maximum seconds to sleep for
$randMin = 5;
$randMax = 10;
$filename = "lock.txt";
$abortOnFlockFail = true;
$sleepForSeconds = 0;
$count = 0;
$errorCount = 0;

function usage()
{
	print($argv[0] . ": [sleep|-h]\n");
	print("  sleep: sleep in seconds once lock is acquired. If sleep is missing or 0, we sleep in random intervals of 1-10 seconds\n");
	print("  -h : print this message\n");
	exit(0);
}

function logMsg(string $msg)
{
	$date = date("H:m:s");
	print($date . ' - ' . $msg . "\n");
}

if ($argv[1] ?? false) {
	if ($argv[1] == '-h') {
		usage();
	}
	$sleepForSeconds = (int)($argv[1]);
}


if (!file_exists($filename)){

    logMsg("create file " . $filename);
    $fp = fopen($filename, "c");
    fwrite($fp, "Write something here\n");
    fflush($fp);
    fclose($fp);

}

while (true) {
    logMsg("in loop ...");	

    logMsg("open file " . $filename . "\n");
    $fp = fopen($filename, "r");
    if (!$fp) {
        logMsg("Error opening file " . $filename);
        exit(1);
    }

    logMsg("acquire exclusive lock (should block if lock in use)");
    if (flock($fp, LOCK_EX)) {
        logMsg("flock succeeded: LOCKED\n");

	if ($sleepForSeconds) {
		$sleepFor = $sleepForSeconds;
	} else {
	        $sleepFor = rand($randMin, $randMax);
	}
        logMsg("now sleep for $sleepFor seconds");
	for ($i=0;$i<$sleepFor;$i++) {
		print($i+1 . ' ... ');
	        sleep(1);
	}
	print("\n");

        logMsg("free flock ...");
        flock($fp, LOCK_UN);
	logMsg("unlock succeeded: UNLOCKED");

        logMsg("now sleep again for $sleepFor seconds");
	for ($i=0;$i<$sleepFor;$i++) {
		print($i+1 . ' ...');
	        sleep(1);
	}
	print("\n");


        $count++;
    } else {
        logMsg("ERROR: flock returns false: This is an error. It should block, if the exclusive lock was already acquired!");
	if ($abortOnFlockFail) {
		print("Aborting  ...\n");
		exit(1);
	}
        $errorCount++;
        $sleepFor = rand($randMin, $randMax);
        logMsg("sleep $sleepFor");
        sleep($sleepFor);
    }
    fclose($fp);

    logMsg("count=$count, errors=$errorCount\n\n");
}



