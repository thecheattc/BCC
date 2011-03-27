<?php
  include ('utility.php');
  include ('../models/visit.php');
  include ('../models/client.php');
  include ('../models/sqldb.php');
  
  if (empty($_GET['client']))
  {
    header('Location: ../search.php?deleteError=1');
  }
  else
  {
    $client = Client::getClientByID($_GET['client']);
    if ($client === NULL)
    {
      header('Location: ../search.php?deleteError=1');
    }
    else
    {
      if ($client->delete() === FALSE)
      {
        header('Location: ../search.php?deleteError=1');
      }
      else
      {
        header('Location: ../search.php?deleteSuccess=1');
      }
    }
  }
