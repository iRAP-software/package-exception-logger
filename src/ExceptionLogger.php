<?php

/* 
 * This class is to make it easy to initialize logging for all uncaught exceptions.
 * All you have to do is create this object. You don't have do anything with it.
 */

namespace iRAP\ExceptionLogger;

class ExceptionLogger
{
    /**
     * Create an exception handler which will be sure to log all uncaught exceptions using the
     * provided logger.
     * @param \iRAP\Logging\LoggerInterface $logger - the logger we will log exceptions to.
     *               the hostname, and maybe the environment (dev, live/production etc)
     * @param string $serviceName - the name of this service to appear in logs. You may want to use
     * @param \iRAP\ExceptionLogger\callable $next - This gets invoked after the exception has been 
     *                                              logged. Useful if you wish to perform your own
     *                                              handling/error message rather than just logging
     * @param int $logLevel - the level of the log. Defaults to 4 for "error".
     * @param array $extraContext - any additional name/value pairs you may wish to have logged.
     */
    public function __construct(\iRAP\Logging\LoggerInterface $logger, string $serviceName, callable $next, int $logLevel=4, array $extraContext=array())
    {
        # Register a shutdown handler to alert the admins if anything goes wrong.
        $exceptionHandler = function(\Throwable $throwable) use ($logger, $logLevel, $extraContext, $serviceName, $next) {
            
            /* @var $exception \Throwable */
            $context = array(
                'service' => $serviceName,
                'message' => $throwable->getMessage(),
                'file'    => $throwable->getFile(),
                'line'    => $throwable->getLine(),
                'code'    => $throwable->getCode(),
                'trace'   => $throwable->getTraceAsString(),
            );
            
            $allContext = array_merge($context, $extraContext);
            $message = "{$serviceName} - Uncaught exception";
            $logger->log($logLevel, $message, $context);
            $next($throwable);
        };
        
        set_exception_handler($exceptionHandler);
    }
}
