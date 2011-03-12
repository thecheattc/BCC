<?php

  //Takes a string, strips it of all non-alphanumeric characters except
  //hyphens and spaces and sanitizes it for the database
  function normalize ($string)
  {
    $string = preg_replace('/[^a-zA-Z0-9\- ]/', '', $string);
    $string = mysql_real_escape_string($string);
    
    return $string;
  }
?>