<?php
  include ('utility.php');
  include ('../models/visit.php');
  include ('../models/familyMember.php');
  include ('../models/client.php');
  include ('../models/sqldb.php');
  
  if (empty($_GET['client']))
  {
    header('Location: ../search.php?deleteError=1');
    exit();
  }
  
  $client = Client::getClientByID($_GET['client']);
  if ($client === NULL)
  {
    header('Location: ../search.php?deleteError=1');
    exit();
  }
  
  if ($client->delete() === FALSE)
  {
    header('Location: ../search.php?deleteError=1');
    exit();
  }
  FamilyMember::cleanFamilyMembers();
  header('Location: ../search.php?deleteSuccess=1');
