<?php
include_once('connection.php');

if (isset($_GET['action']) && $_GET['action'] == 'getData') {

  $data = json_decode(file_get_contents("php://input"));
  $geneName = $data -> geneName;

  $result = array(
    array(
      'x'          => array('11', '21', '31', '41'),
      'y'          => array('11', '12', '13', '14'),
      'mode'       => 'markers',
      'marker'     => array(
                        'color' => array('rgb(93, 164, 214)', 'rgb(255, 144, 14)',  'rgb(44, 160, 101)', 'rgb(255, 65, 54)'),
                        'opacity' => array(1, 0.8, 0.6, 0.4), // 注意这里不能加引号
                        'size' => array('30', '40', '50', '60'),
                      ),
    )
  );

  echo json_encode($result);

  exit();
}

?>
