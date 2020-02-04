<?php
/*
 * Uses exclusive and non-blocking lock (like TYPO3)
 */

// minimum / maximum seconds to sleep for
$randMin = 1;
$randMax = 10;
$filename = "lock.txt";
$abortOnFlockFail = true;

$count = 0;
$errorCount = 0;

class Locker
{

        protected $fp = null;
	protected $filename = "lock.txt";

	public function acquire()
	{

	    print("open file " . $this->filename . "\n");
	    $this->fp = fopen($this->filename, "c");
	    if (!$this->fp) {
		print("could not open file");
		exit(1);
	    } 
            	
            if (flock($this->fp, LOCK_EX | LOCK_NB, $wouldblock)) {
	        print("flock succeeded\n");
                return true; 
            } 		
            print("flock returns false - wouldblock=" . ($wouldblock ? 'true' : 'false') . "\n");
            if ($wouldblock) {
                print("ok: wouldblock is true ... flock would block ... lock in use, must wait\n");
		return true;
            } else {
                print("ERROR: wouldblock is false\n");
		exit(1);
            }
	    return false;
		            		
	}

	public function release()
	{
		print("free flock\n");
		if ($this->fp) {
	                flock($this->fp, LOCK_UN);
			fclose($this->fp);
			$this->fp = null;
		}
	}

}

$locker = new Locker();

while (true) {
   print("in loop ...\n");	


    print("acquire exclusive lock (should block if lock in use)\n");
    if ($locker->acquire()) {
        print("acquire succeeded\n");

	$sleepFor = rand($randMin, $randMax);
        print("now sleep $sleepFor\n");
        sleep($sleepFor);
        $locker->release();

    } else {
	print("ERROR:acquire returns false ..\n");
    }



    $sleepFor = rand($randMin, $randMax);
    print("now sleep $sleepFor\n");
    sleep($sleepFor);


    $count++;
}

printf("count=$count, errors=$errorCount\n\n");




