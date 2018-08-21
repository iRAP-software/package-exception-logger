# Exception Logger
A package to capture uncaught exceptions and log them using the provided logger. 
All you have to do is create the object like so:

```php
new \iRAP\ExceptionLogger\ExceptionLogger(
    $logger, // objec tof LoggerInterface
    "My Service", 
    $nextExcptionHandler=function(Throwable $e) { /*  do nothing */ }
);
```

This will result in any uncaught exceptions being logged. The object works by setting the exception 
handler so it won't have any effect if you call set_exception_handler after having created the object.

If you want to run your own exception handler, you can just have it be called by the callback as 
shown below:

```php
/* @var $logger LoggerInterface */
$nextExcptionHandler = function(Throwable $e) { 
    // my custom uncaught exception handling goes here.
};

new \iRAP\ExceptionLogger\ExceptionLogger(
    $logger,
    "My Service", 
    nextExcptionHandler
);
```

If you don't have a custom exception handler, I would recommend restoring PHP's default exception
handling by doing the following:

```php
$next = function(Throwable $e) { 
    restore_exception_handler();
    throw $e; //This triggers the previous exception handler
};

new \iRAP\ExceptionLogger\ExceptionLogger(
    $logger,
    "My Service", 
    $next
);
```

