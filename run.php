<?php

    require_once 'src/ZMQ/Connector.php';
    require_once 'src/Interpreter/Interpreter.php';

    $connector = new Connector("127.0.0.1");
    $interpreter = new Interpreter();

    while(true)
    {
        $query = $line = trim(fgets(STDIN));
        if($query == "exit;")break;

        $ans = $interpreter->parse($query);

        if($ans == "error")
        {
            echo "Syntaxis Error\n";
            continue;
        }
        echo $connector->send($ans) . "\n";
    }
?>