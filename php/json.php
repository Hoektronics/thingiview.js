<?php

// server needs a good cpu!  Might need to make timeout higher if server chokes on really really big models...
set_time_limit(3000);

include('convert.php');

$file = $_GET['file'];

// NC: I found that my STL files weren't being recognized as binary, so I'm adding a flag
$binary = ($_GET['binary'] == '') ? false : true;

$file_parts = pathinfo($file);
$handle = fopen($file, 'rb');
if ($handle == FALSE)
{
  trigger_error("Failed to open file $file");
  exit;
}

switch($file_parts['extension'])
{
  case 'stl':
    if ($binary)
    {
      $result = parse_stl_binary($handle);
    }
	else
    {
	  $contents = getStringContents($handle);
      if ( stripos($contents, 'solid') === FALSE )
	    $result = parse_stl_binary($handle);
	  else
	    $result = parse_stl_string($contents);
    }  
    break;
  case 'obj':
    $result = parse_obj_string(getStringContents($file));
    break;
}

echo json_encode($result);

// NC: moved the string parser to a function
function getStringContents($handle)
{
  $contents = "";

  while (!feof($handle))
    $contents .= fgets($handle);

  return preg_replace('/$\s+.*/', '', $contents);
}