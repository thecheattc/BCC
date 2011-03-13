<?php

  //Strips a string of all non-alphanumeric characters except hyphens and spaces
  function processString ($string)
  {
    $string = preg_replace('/[^a-zA-Z0-9\- ]/', '', $string);
    if (empty($string))
    {
      $string = NULL;
    }
    return $string;
  }
  
  //Converts a date string to Year-Month-Day format for MySQL.
  //This expects the string to be in a MM-DD-YYYY format.
  function processDate($dateString = '')
  {
    //Strip any non-numeric characters
    $dateString = preg_replace('/[^0-9]/', '', $dateString);
    if (strlen($dateString) != 8)
    {
      return NULL;
    }
    $year = substr($dateString, 4, 4);
    $month = substr($dateString, 0, 2);
    $day = substr($dateString, 2, 2);
    return $year . "-" . $month . "-" . $day;
  }
?>