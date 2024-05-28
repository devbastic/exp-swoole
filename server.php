<?php

function pretty ($var) {
  return gettype($var) . ' ' . json_encode(
    $var,
    JSON_UNESCAPED_SLASHES |       // Don't escape forward slashes. stripslashes() could be used afterwards instead
    JSON_UNESCAPED_UNICODE |       // Print unicode characters insteas of their encoding "€" vs "\u20ac"
    JSON_PRETTY_PRINT |            // Nice layout over several lines, human readable
    JSON_PARTIAL_OUTPUT_ON_ERROR | // Substitute whatever can not be printed
    JSON_INVALID_UTF8_SUBSTITUTE   // Convert invalid UTF-8 characters to \0xfffd (Unicode Character 'REPLACEMENT CHARACTER')
  );                               // Constants: https://www.php.net/manual/en/json.constants.php
}


$server = new OpenSwoole\HTTP\Server("127.0.0.1", 9503);

$server->set([
    'worker_num' => 4,      // The number of worker processes to start
    'task_worker_num' => 4,  // The amount of task workers to start
    'backlog' => 128       // TCP backlog connection number
]);


// Triggered when new worker processes starts

$server->on("WorkerStart", function($server, $id)
{
    echo '[worker started, id=' . $id . ' starting]' . PHP_EOL;
});


// Triggered when the HTTP Server starts, connections are accepted after this callback is executed

$server->on("start", function($server)
{
    echo "[http server starting]" . PHP_EOL;
});


$server->on("Task", function($server, $id)
{
    echo '[task id=' . $id . ' starting]' . PHP_EOL;
});



// The main HTTP server request callback event, entry point for all incoming HTTP requests
$server->on('request', function(OpenSwoole\Http\Request $request, OpenSwoole\Http\Response $response)
{
    //echo "[request]" . PHP_EOL;
	echo '.';

	//echo pretty($request);

    $response->end('<h1>Hello World!</h1>');
});
	

	
// Triggered when the server is shutting down
$server->on("Shutdown", function($server, $workerId)
{
    echo "[SHUTDOWN]" . PHP_EOL;
});

// Triggered when worker processes are being stopped
$server->on("WorkerStop", function($server, $workerId)
{
    echo "[worker STOP]" . PHP_EOL;
});

$server->start();

