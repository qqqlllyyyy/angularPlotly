<?php
include_once('connection.php');

if (isset($_GET['action']) && $_GET['action'] == 'getData') {

  $data              = json_decode(file_get_contents("php://input"));
  $GENE_NAME         = $data -> geneName;
  $Y_FIELD           = $data -> yField;
  $COLORING_FIELD    = $data -> colorField;
  $result            = array();


  // ---------------------------------------------------------------------------
  // Check Parameters
  // ---------------------------------------------------------------------------

  // 1. Duplicate field
	if ($Y_FIELD == $COLORING_FIELD) {
    $result['message']         = 'Error';
    $result['messageDetail']   = 'Error: Y-axis field has to be different from the coloring field.';
	}
  // 2. Get geneIndex
	$sql = "SELECT `GeneIndex` FROM `{$BXAF_CONFIG['TBL_GENEANNOTATION']}` WHERE `GeneName`='{$GENE_NAME}'";
	$GENE_INDEX = $DB -> get_one($sql);
	if (!isset($GENE_INDEX) || trim($GENE_INDEX) == '' || intval($GENE_INDEX) < 0) {
    $result['message']         = 'Error';
    $result['messageDetail']   = 'Error: No gene found.';
	}
  // 3. If error
  if ($result['message'] == 'Error') {
    echo json_encode($result);
    exit();
  }



  // ---------------------------------------------------------------------------
  // Find Field Options and # Appearance
  // ---------------------------------------------------------------------------

  // 1. All comparisons for current gene
  $sql = "SELECT `ComparisonIndex`, `Log2FoldChange`, `PValue`, `AdjustedPValue`
			FROM `{$BXAF_CONFIG['TBL_COMPARISONDATA']}`
			WHERE `GeneIndex`=" . $GENE_INDEX;
	$data_comparison = $DB -> get_all($sql);

  $Y_FIELD_LIST               = array();
	$COLORING_FIELD_LIST        = array();
	$Y_FIELD_NUMBER             = array(); // Appear times
	$COLORING_FIELD_NUMBER      = array();

  // 2. Loop through each comparison data
  foreach ($data_comparison as $comparison) {

		$sql = "SELECT `{$Y_FIELD}`, `{$COLORING_FIELD}`
            FROM `{$BXAF_CONFIG['TBL_COMPARISONS']}`
            WHERE `ComparisonIndex`=" . $comparison['ComparisonIndex'];
		$comparison_row = $DB -> get_row($sql);

		if (trim($comparison['Log2FoldChange']) == ''
			|| trim($comparison['Log2FoldChange']) == '.'
			|| trim($comparison['Log2FoldChange']) == 'NA'
			|| trim($comparison['PValue']) == ''
			|| trim($comparison['PValue']) == '.'
			|| trim($comparison['PValue']) == 'NA'
			|| trim($comparison_row[$Y_FIELD]) == ''
			|| trim($comparison_row[$Y_FIELD]) == 'NA'
			|| trim($comparison_row[$COLORING_FIELD]) == ''
			|| trim($comparison_row[$COLORING_FIELD]) == 'NA') {
			continue;
		}

		// Count number of appearance
		if (!in_array($comparison_row[$Y_FIELD], array_keys($Y_FIELD_NUMBER))) {
			$Y_FIELD_NUMBER[$comparison_row[$Y_FIELD]] = 1;
		} else {
			$Y_FIELD_NUMBER[$comparison_row[$Y_FIELD]] += 1;
		}
		if (!in_array($comparison_row[$COLORING_FIELD], array_keys($COLORING_FIELD_NUMBER))) {
			$COLORING_FIELD_NUMBER[$comparison_row[$COLORING_FIELD]] = 1;
		} else {
			$COLORING_FIELD_NUMBER[$comparison_row[$COLORING_FIELD]] += 1;
		}
	}

  arsort($Y_FIELD_NUMBER);
	arsort($COLORING_FIELD_NUMBER);








  // // If something is wrong
  // // ---------------------------------------------
  // $result['message'] = 'Error';
  // $result['messageDetail'] = 'Something wrong.';
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
