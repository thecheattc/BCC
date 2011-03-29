<?php

  //Strips a string of all non-alphanumeric characters except hyphens and spaces
  function processString ($string, $stripSpaces = FALSE)
  {
    $string = trim($string);
    if ($stripSpaces)
    {
      $string = str_replace(' ', '', $string);
    }
    $string = preg_replace('/[^a-zA-Z0-9\- ]/', '', $string);
    if ($string !== "0" && empty($string))
    {
      $string = NULL;
    }
    return $string;
  }
  
  //Converts a date string to Year-Month-Day format for MySQL.
  //This expects the string to be in a MM-DD-YYYY format.
  function normalDateToMySQL($dateString = '')
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
  
  //Converts a date string from YYYY-MM-DD to MM-DD-YYYY
  function mySQLDatetoNormal($dateString = '')
  {
    if (strlen($dateString) != 10)
    {
      return NULL;
    }
    $year = substr($dateString, 0, 4);
    $month = substr($dateString, 5, 2);
    $day = substr($dateString, 8, 2);
    return $month . "-" . $day . "-" . $year;
  }
?>