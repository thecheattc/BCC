<?php
  include ('utility.php');
  include ('../models/visit.php');
  include ('../models/sqldb.php');
  
  
  if (empty($_GET['visit']) || empty($_POST['date']) || empty($_POST['type']))
  {
    header("Location: ../search.php?error=1");
  }
  else
  {
    $visit = Visit::getVisitByID($_GET['visit']);
    if ($visit === NULL)
    {
      header("Location: ../editVisit.php?visit={$_GET['visit']}&error=1");
    }
    $visit->setDate(normalDateToMySQL($_POST['date']));
    $visit->setTypeID($_POST['type']);

    if ($visit->save() === FALSE)
    {
      header("Location:../editVisit.php?visit={$_GET['visit']}&error=1");
    }
    else
    {
      header("Location:../editVisit.php?visit={$_GET['visit']}&success=1");
    }
  }