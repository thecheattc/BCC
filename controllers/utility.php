<?php

  //Strips a string of all non-alphanumeric characters except hyphens and spaces
  function processString ($string)
  {
    $string = preg_replace('/[^a-zA-Z0-9\- ]/', '', $string);    
    return $string;
  }
  
  //Converts a date string to a timpestamp
  function processDate($dateString = '')
  {
    if (($timestamp = strtotime($dateString)) === false)
    {
      $timestamp = NULL;
    }
    return $timestamp;
  }
?>