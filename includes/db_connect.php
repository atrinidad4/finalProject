<?php
     define('DB_DSN','mysql:host=172.19.0.1;dbname=serverside;charset=utf8');
     define('DB_USER','serveruser');
     define('DB_PASS','pass123');     
     
     try {
         $db = new PDO(DB_DSN, DB_USER, DB_PASS);
         $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     } catch (PDOException $e) {
         print "Error: " . $e->getMessage();
         die();
     }