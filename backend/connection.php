<?php

ini_set('zlib.output_compression','On');
ini_set('display_errors', '0');
ini_set('session.auto_start', '0');
ini_set('session.use_cookies', '1');
ini_set('session.gc_maxlifetime', 43200);
ini_set('register_globals', '0');
ini_set('auto_detect_line_endings', true);

session_set_cookie_params (0);
session_cache_limiter('nocache');
session_name('BXAF_' . md5(dirname(__FILE__)));
session_start();
error_reporting(0);

include_once("library/bxaf_mysqli.min.php");


// Database Connection
$db_settings = array(
	'user'    => 'db_pfizer',
	'pass'    => 'Pfizer@2016',
	'db'      => 'db_diseaseland_partial'
);

$DB = new bxaf_mysqli($db_settings);
$DB->Execute("SET NAMES 'utf8' COLLATE 'utf8_general_ci'");
$DB->SetFetchMode(ADODB_FETCH_ASSOC);

// Config Settings

// Set user ID
$BXAF_CONFIG['BXAF_USER_CONTACT_ID'] = '0';

// Define related database table names
$BXAF_CONFIG['TBL_COMPARISONDATA']            = 'ComparisonData';
$BXAF_CONFIG['TBL_COMPARISONS']               = 'Comparisons';
$BXAF_CONFIG['TBL_GENEANNOTATION']            = 'GeneAnnotation';
$BXAF_CONFIG['TBL_GENELIST']             = 'HumanGeneList';
$BXAF_CONFIG['TBL_PROJECTS']                  = 'Projects';
$BXAF_CONFIG['TBL_SAMPLES']                   = 'Samples';
$BXAF_CONFIG['TBL_USERPREFERENCE']            = 'UserPreference';
$BXAF_CONFIG['TBL_GENECOMBINED']              = 'GeneCombined';


// $sql = "SELECT * FROM `{$BXAF_CONFIG['TBL_COMPARISONS']}` WHERE `ComparisonIndex`=15";
// $data = $DB -> get_all($sql);
// print_r($data);

// $array = array('1'=> '2', '2' => '3');
// echo serialize($array);
// exit();
