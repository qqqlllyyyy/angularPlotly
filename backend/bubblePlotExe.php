<?php
include_once('connection.php');

if (isset($_GET['action']) && $_GET['action'] == 'getData') {

  $data = json_decode(file_get_contents("php://input"));
  $geneName = $data -> geneName;
  $yField = $data -> yField;
  $colorField = $data -> colorField;
  $result = array();


  // If something is wrong
  // ---------------------------------------------
  // $result['message'] = 'Error';
  // $result['messageDetail'] = 'Something wrong.' . $yField . $colorField;
  // echo json_encode($result);
  // exit();





  $result = array();
  $result['message'] = 'Success';

  $x = array();
  $y = array();
  for ($i = 1; $i < 30; $i++) {
    $x[] = $i;
    $y[] = $i;
  }

  $result['plotData'] = array(
    array(
      'x'          => $x,
      'y'          => $y,
      'mode'       => 'markers',
      // 'marker'     => array(
      //                   'color' => array('rgb(93, 164, 214)', 'rgb(255, 144, 14)',  'rgb(44, 160, 101)', 'rgb(255, 65, 54)'),
      //                   'opacity' => array(1, 0.8, 0.6, 0.4), // 注意这里不能加引号
      //                   'size' => array('30', '40', '50', '60'),
      //                 ),
    )
  );

  echo json_encode($result);

  exit();
}

?>
