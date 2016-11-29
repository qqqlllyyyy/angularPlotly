<?php
include_once('connection.php');





/******************************************************************************
 * Generate Chart
 * ----------------------------------------------------------------------------
 *
 * 1. Check Parameters
 *
 *    1.1 Check duplicate field
 *    1.2 Get geneIndex
 *    1.3 If error occurs, generate output
 *
 * 2. Find Field Options and # Appearance
 *
 *    2.1 All comparisons for current gene
 *    2.2 Loop through each comparison data
 *
 * 3. Filter Y and Coloring Field by Display Settings
 *
 *    3.1 Y-axis display setting
 *    3.2 Color display setting
 *
 * 4. Get Values Grouped by Color Settings
 *
 *    4.1 Fetch comparison data
 *    4.2 Skip unknown records
 *    4.3 Skip unselected y&coloring option
 *    4.4 Save marker info used in chart
 *
 * 5. Prepare for Success Output
 *
 *    5.1 Success output header and data
 *    5.2 Parameter needed for each point
 *    5.3 Chart data output
 *    5.4 Layout output
 *
 ******************************************************************************/

if (isset($_GET['action']) && $_GET['action'] == 'getData') {

  $data                     = json_decode(file_get_contents("php://input"));
  $GENE_NAME                = $data -> geneName;
  $Y_FIELD                  = $data -> yField;
  $COLORING_FIELD           = $data -> colorField;
  $MARKER_AREA              = $data -> markerArea;
  $Y_DISPLAY_SETTING        = $data -> yDisplay;
  $COLOR_DISPLAY_SETTING    = $data -> colorDisplay;
  $CHART_WIDTH              = $data -> width;

  $result                   = array();


  // ---------------------------------------------------------------------------
  // 1. Check Parameters
  // ---------------------------------------------------------------------------

  // 1.1 Duplicate field
	if ($Y_FIELD == $COLORING_FIELD) {
    $result['message']         = 'Error';
    $result['messageDetail']   = 'Error: Y-axis field has to be different from the coloring field.';
	}
  // 1.2 Get geneIndex
	$sql = "SELECT `GeneIndex` FROM `{$BXAF_CONFIG['TBL_GENEANNOTATION']}` WHERE `GeneName`='{$GENE_NAME}'";
	$GENE_INDEX = $DB -> get_one($sql);
	if (!isset($GENE_INDEX) || trim($GENE_INDEX) == '' || intval($GENE_INDEX) < 0) {
    $result['message']         = 'Error';
    $result['messageDetail']   = 'Error: No gene found.';
	}
  // 1.3 If error occurs, generate output
  if ($result['message'] == 'Error') {
    echo json_encode($result);
    exit();
  }



  // ---------------------------------------------------------------------------
  // 2. Find Field Options and # Appearance
  // ---------------------------------------------------------------------------

  // 2.1 All comparisons for current gene
  $sql = "SELECT `ComparisonIndex`, `Log2FoldChange`, `PValue`, `AdjustedPValue`
			FROM `{$BXAF_CONFIG['TBL_COMPARISONDATA']}`
			WHERE `GeneIndex`=" . $GENE_INDEX;
	$data_comparison = $DB -> get_all($sql);

  $Y_FIELD_LIST               = array();
	$COLORING_FIELD_LIST        = array();
	$Y_FIELD_NUMBER             = array(); // Appear times
	$COLORING_FIELD_NUMBER      = array();

  // 2.2 Loop through each comparison data
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



  // ---------------------------------------------------------------------------
  // 3. Filter Y and Coloring Field by Display Settings
  // ---------------------------------------------------------------------------

  // 3.1 Y-axis display setting
  if ($Y_DISPLAY_SETTING == 'top10') {
    $index = 0;
    foreach($Y_FIELD_NUMBER as $key => $value) {
      if ($index >= 10) {
        unset($Y_FIELD_NUMBER[$key]);
      }
      $index++;
    }
  }
  if ($Y_DISPLAY_SETTING == 'top20') {
    $index = 0;
    foreach($Y_FIELD_NUMBER as $key => $value) {
      if ($index >= 20) {
        unset($Y_FIELD_NUMBER[$key]);
      }
      $index++;
    }
  }

  // 3.2 Color display setting
  if ($COLOR_DISPLAY_SETTING == 'top10') {
    $index = 0;
    foreach($COLORING_FIELD_NUMBER as $key => $value) {
      if ($index >= 10) {
        unset($COLORING_FIELD_NUMBER[$key]);
      }
      $index++;
    }
  }
  if ($COLOR_DISPLAY_SETTING == 'top20') {
    $index = 0;
    foreach($COLORING_FIELD_NUMBER as $key => $value) {
      if ($index >= 20) {
        unset($COLORING_FIELD_NUMBER[$key]);
      }
      $index++;
    }
  }



  // ---------------------------------------------------------------------------
  // 4. Get Values Grouped by Color Settings
  // ---------------------------------------------------------------------------

  $ALL_MARKER = array();
	$ALL_GENES = array();
	$ALL_APPEARED_Y = array();

  foreach ($data_comparison as $comparison) {

    // 4.1 Fetch comparison data
    $sql = "SELECT `{$Y_FIELD}`, `{$COLORING_FIELD}`, `ComparisonID`, `ComparisonIndex`
            FROM `{$BXAF_CONFIG['TBL_COMPARISONS']}`
            WHERE `ComparisonIndex`=" . $comparison['ComparisonIndex'];
		$comparison_row = $DB -> get_row($sql);

    // 4.2 Skip unknown records
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

    // 4.3 Skip unselected y&coloring option
    $y_temp = $comparison_row[$Y_FIELD];
		$color_temp = $comparison_row[$COLORING_FIELD];
    if (!in_array($y_temp, array_keys($Y_FIELD_NUMBER))
			|| !in_array($color_temp, array_keys($COLORING_FIELD_NUMBER))) {
			continue;
		}
    // Save appeared y option and point info
		if (!in_array($y_temp, $ALL_APPEARED_Y)) {
			$ALL_APPEARED_Y[] = $y_temp;
		}

    // 4.4 Save marker info used in chart
    if (!in_array($color_temp, array_keys($ALL_MARKER))) {
			$ALL_MARKER[$color_temp] = array(
				array(
					'Y_FIELD' =>$y_temp,
					'COLORING_FIELD' => $color_temp,
					'LOGFC' => $comparison['Log2FoldChange'],
					'PValue' => $comparison['PValue'],
					'AdjustedPValue' => $comparison['AdjustedPValue'],
					'COMPARISON_ID' => $comparison_row['ComparisonID'],
					'COMPARISON_INDEX' => $comparison_row['ComparisonIndex']
				)
			);
		} else {
			$ALL_MARKER[$color_temp][] = array(
				'Y_FIELD' =>$y_temp,
				'COLORING_FIELD' => $color_temp,
				'LOGFC' => $comparison['Log2FoldChange'],
				'PValue' => $comparison['PValue'],
				'AdjustedPValue' => $comparison['AdjustedPValue'],
				'COMPARISON_ID' => $comparison_row['ComparisonID'],
				'COMPARISON_INDEX' => $comparison_row['ComparisonIndex']
			);
    }
  }

  asort($ALL_APPEARED_Y);
	$HEIGHT = max(700, count($ALL_APPEARED_Y) * 30);



  // ---------------------------------------------------------------------------
  // 5. Prepare for Success Output
  // ---------------------------------------------------------------------------

  // 5.1 Success output header and data
  $result = array();
  $result['message'] = 'Success';
  $result['plotData'] = array();

  foreach ($ALL_MARKER as $key => $value) {

    // 5.2 Parameter needed for each point
    $x_list                     = array();    // X-coordinate, log fold change
    $y_list                     = array();    // Y-coordiange, based on y-field
    $hover_text_list            = array();    // Hover text information
    $area_list                  = array();    // Area of the marker (radius)
    $comparison_index_list      = array();    // Comparison index saved in the frontend
    $comparison_id_list         = array();    // Comparison id saved in the frontend

    foreach($value as $k => $v) {
      // x & y
      $x_list[]                 = $v['LOGFC'];
      $y_list[]                 = addslashes($v['Y_FIELD']);
      // text on hover
      $text_temp                = "Comparison ID: " . $v['COMPARISON_ID']  . "<br />";
      $text_temp               .= substr($Y_FIELD, strpos($Y_FIELD, '_')+1) . ": ";
      $text_temp               .= addslashes($v['Y_FIELD']) . "<br />";
      $text_temp               .= substr($COLORING_FIELD, strpos($COLORING_FIELD, '_')+1) . ": ";
      $text_temp               .= addslashes($v['COLORING_FIELD']) . "<br />";
      $text_temp               .= "P-value: " . $v['PVALUE'] . "<br />";
      $text_temp               .= "Adj P-value: " . $v['ADJPVALUE'] . "<br />";
      $text_temp               .= "logFC: " . $v['LOGFC'];
      $hover_text_list[]        = $text_temp;
      // area
      $area                     = $v[$MARKER_AREA];
      if ((-1000) * log10($area) < 5000
          && (-1000) * log10($area) > 100) {
				$area_list[]            = (-1000) * log10($area);
			}
      else if ((-1000) * log10($area) > 5000) {
				$area_list[]            = 5000;   // Marker upper bound
			}
      else {
				$area_list[]            = 100;   // Marker lower bound
			}
      // comparison info
      $comparison_index_list[]  = $v['COMPARISON_INDEX'];
			$comparison_id_list[]     = $v['COMPARISON_ID'];
    }

    // 5.3 Chart data output
    $result['plotData'][] = array(
      'x'                       => $x_list,
      'y'                       => $y_list,
      'mode'                    => 'markers',
      'hoverinfo'               => 'text',
      'name'                    => str_replace(';', '<br>', $key),
      'text'                    => $hover_text_list,
      'marker'                  => array(
                                    'size'              => $area_list,
                                    'sizeref'           => 7,
                                    'sizemode'          => 'area',
                                    'comparison_index'  => $comparison_index_list,
                                    'comparison_id'     => $comparison_id_list
                                  ),
    );
  }

  // 5.4 Layout output
  $result['layout'] = array(
    'height'                    => $HEIGHT,
    'width'                     => $CHART_WIDTH,
    'hovermode'                 => 'closest',
    'title'                     => 'Bubble Chart for ' . $GENE_NAME . '<br />Colored by ' . $COLORING_FIELD,
    'margin'                    => array('l' => 150),
    'xaxis'                     => array('title' => 'Log 2 Fold Change'),
    'yaxis'                     => array(
                                    'title' => $Y_FIELD,
                                    'categoryorder' => 'category ascending'
                                  ),
  );



  echo json_encode($result);

  exit();
}



?>
