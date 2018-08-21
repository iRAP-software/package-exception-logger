<?php

/*
 * Raise an exception and then manually check the database for the exception.
 * @TODO - figure out a way we can have test check the database for the exception after
 * the exception handler caught it. 
 */

class TestException extends AbstractTest
{
    public function getDescription(): string 
    {
        return "Test the logging of an uncaught exception.";
    }
    
    
    public function run() 
    {
        $this->init();
        $mysqliConn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
        $logger = new \iRAP\Logging\DatabaseLogger($mysqliConn, DB_LOGS_TABLE);
        
        $next = function(Throwable $e) { 
            restore_exception_handler();
            throw $e;
        };
        
        $exceptionLogger = new \iRAP\ExceptionLogger\ExceptionLogger(
            $logger, 
            "Exception tester",
            $next
        );
        
        throw new Exception("This is my uncaught exception");
    }
    
    
    /**
     * Prepare the environment for the test.
     */
    private function init()
    {
        // prep the db
        $mysqliConn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

        $query = 
            "CREATE TABLE IF NOT EXISTS `" . DB_LOGS_TABLE . "` ( " .
                "`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'unique identifier', " .
                "`message` text NOT NULL COMMENT 'the actual error message', " .
                "`context` longtext NOT NULL COMMENT 'json string of context for the error (see logging standards)', " .
                "`priority` int(1) NOT NULL COMMENT 'priority level, higher = more important', " .
                "`when` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, " .
                "PRIMARY KEY (`id`), " .
                "KEY `priority` (`priority`), " .
                "KEY `when` (`when`) " .
            ") ENGINE=InnoDB COMMENT='table for logging errors.'";

        $mysqliConn->query($query) or die("Failed to create the logs table for testing.");
        $mysqliConn->query("TRUNCATE `" . DB_LOGS_TABLE . "`") or die("Failed to create the logs table for testing.");
    }
}
