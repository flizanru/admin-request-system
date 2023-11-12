<?php

// Система ниже не являеться обязательной. Просто подключаете функцию Mail в PHP и всё.
// The system below is optional. Just connect the Mail function in PHP and that's it.














// // Подключение к базе данных
// $ebal = new mysqli('localhost', 'cybernuts_admin', '', 'cybernuts_admin');

// $sql = "CREATE TABLE IF NOT EXISTS emails (
//   id INT AUTO_INCREMENT PRIMARY KEY,
//   date DATETIME,
//   from_email VARCHAR(255),
//   to_email VARCHAR(255), 
//   subject VARCHAR(255),
//   message TEXT,
//   done TINYINT(1)
// )";

// $ebal->query($sql); 

// function sendMail($to, $subject, $message, $from) {

//   global $ebal;

//   $date = date('Y-m-d H:i:s');  
//   $sql = "INSERT INTO emails SET date='$date', from_email='$from', to_email='$to', subject='$subject', message='$message', done=0";

//   $ebal->query($sql);
 
//   $ch = curl_init();
//   curl_setopt($ch, CURLOPT_URL, '');
//   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
//   curl_exec($ch);
//   curl_close($ch);

// }