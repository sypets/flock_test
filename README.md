These programs can be used to test PHP flock() functionality, as used in default
TYPO3 locking strategy. 

You can test this, by running it:

* once on one system
* several times on one or more systems
 

In any case, it should not abort with an error. If it does, there is something
wrong with flock. 


The programs will create a file lock.txt in the current directory. In order
to test, all instances should run in the same directory (so that the same lock
file is used). 

System 1 can be the same server as System 2.

# Usage

## flock_test_exclusive_blocking.php

This acquires an EXCLUSIVE lock with BLOCKING. This means the program will wait, if lock is
already in use. 

Usage: 

  php -f flock_test_exclusive_blocking.php 100

Waits for 100 seconds while holding the lock and after releasing the lock. If no argument is
passed, a random number is used.

  php -f flock_test_exclusive_blocking.php -h

Show help

System 1:

  php -f flock_test_exclusive_blocking.php


System 2 (while program is running on System 1):


  php -f flock_test_exclusive_blocking.php
  
  
## flock_test_exclusive_nonblocking.php

This is the same as the other program, except that this is EXCLUSIVE and NON-BLOCKING. This
means flock will return immediately, whether lock is available or not. 

Use this in the same way, except without argument:

  php -f flock_test_exclusive_nonblocking.php

----------


Testing with Linux command line utility flock

> The flock command is part of the util-linux package and is available from Linux Kernel Archive ⟨ftp://ftp.kernel.org/pub/linux/utils/util-linux/⟩.

exclusive, blocking:

    flock -x lock.txt sleep 10
    
Should block if lock is in use.     
    
    
exclusive, non-blocking

    flock -x -n -E 10 lock.txt sleep 10
    
Will return immediately with return code 10, if lock is in use.     

