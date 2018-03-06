<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UCS-4">
    <title>Socket_Programing</title>
</head>
<body>
<!--$key = number of clients
$clients = clients connection list
$socket_client = clients connection list with id in database-->
<?php
$address = '127.0.0.1';
$port = 40403;
$max_clients = 10000;
set_time_limit(0);
// Array that will hold client information
$clients = Array();
// Create a TCP Stream socket
$master_socket = socket_create(AF_INET, SOCK_STREAM, 0);
// Bind the socket to an address/port
socket_bind($master_socket, $address, $port);
// Start listening for connections
socket_listen($master_socket);
// master loop
while (true) {
    $aaa++;
    // Setup clients listen socket for reading
    $read = array();
    $read[] = $master_socket;
    // Add clients to the $read array
    foreach ($clients as $client) {
        $read[] = $client;
    }
    // Set up a blocking call to socket_select()
    $ready = socket_select($read, $read_null = null, $read_null = null, $read_null = null);
    if ($ready == 0) {
        continue;
    }

    // if a new connection is being made add it to the client array
    if (in_array($master_socket, $read)) {
        if (count($clients) <= $max_clients) {
            /*echo "accept client...\n";*/
            $clients[] = socket_accept($master_socket);
        } else {
            echo "max clients reached...\n";
        }
        // remove master socket from the read array
        $key = array_search($master_socket, $read);
        unset($read[$key]);
    }
    /*echo "client list:\n";*/
    $socket_client = array();
    $ip = client_ip();
    $a = 0;
    $answer = array();
    foreach ($clients as $client1) {
        $socket_client[] = array("names" => $client1, "id" => $a);
        $a++;
    }
    // If a client is trying to write - handle it now
    foreach ($clients as $client) {
        $input = socket_read($client, 1024 * 1024);
        // Zero length string meaning disconnected
        if ($input == null) {
            $key = array_search($client, $clients);
            unset($clients[$key]);
        } else {
            //read hex data
            $hex=unpack("H*",$input);
            //print hex data
            var_dump($hex[1]);
            $str = hexToStr($hex[1]);
        }
        $n = trim($input);
        if ($input) {
            socket_write($clients[$key], $str[0]);
        }
    }
} // eo master loop
// Close the master sockets
socket_close($master_socket);

?>
</body>
</html>
