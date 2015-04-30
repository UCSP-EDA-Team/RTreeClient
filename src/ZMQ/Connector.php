<?php

Class Connector{

    private $socket;

    public function __construct($hostname)
    {
        /* Server hostname */
        $dsn = "tcp://". $hostname . ":2116";

        /* Create a socket */
        $this->socket = new \ZMQSocket(new \ZMQContext(), \ZMQ::SOCKET_REQ, 'my socket');

        /* Get list of connected endpoints */
        $endpoints = $this->socket->getEndpoints();

        /* Check if the socket is connected */
        if (!in_array($dsn, $endpoints['connect'])) {
            $this->socket->connect($dsn);
        } else {
            //echo "<p>Already connected to $dsn</p>";
        }
    }

    public function send($data)
    {
        $message = base64_encode(json_encode($data));
        $this->socket->send($message);
        $message = $this->socket->recv();

        $message = base64_decode($message);
        return $message;
    }
}