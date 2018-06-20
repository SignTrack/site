<?php

if (!function_exists('dump')) {
    function dump ($var, $label = 'Dump', $echo = TRUE) {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
        $output = '<pre style="background: #FFFEEF; color: #000; border: 1px dotted #000; padding: 10px; margin: 10px 0; text-align: left; width: 100% !important; font-size: 12px !important;">' . $label . ' => ' . $output . '</pre>';
        if ($echo == TRUE) {
            echo $output;
        }
        else {
            return $output;
        }
    }
}
if (!function_exists('dump_exit')) {
    function dump_exit($var, $label = 'Dump', $echo = TRUE) {
        dump ($var, $label, $echo);exit;
    }
}

define('DEBUG_CHECK', FALSE);

function _e($err, $label = "DEBUG") {
    if (DEBUG_CHECK) {
        dump($err, $label);
    }
}


require_once('table-abstract.php');
require_once('db.php');
require_once('fake-data.php');


// dump($_GET, $_GET);

$cols = $cyfeTable->get_cols();
$group_by = NULL;


// rand_populate_db(1000);

/**
 * Creates a donut graph that compares the closed revenue
 * for each representative.
 */
if (isset($_GET['donut']) && $_GET['donut'] === 'revenuePerRep') {
    $cols = $cyfeTable->select(array('representative', 'revenue'));
    $group_by = $cyfeTable->group_by('representative');
    $cyfeTable->where('close_date', '0000-00-00', '!=');

    $cyfeTable->reset();
    $s = $cyfeTable->statement('SELECT representative, SUM(revenue) FROM cyfe WHERE close_date != "0000-00-00" GROUP BY representative', TRUE);

    $results = $cyfeTable->get();
    $sum = 0;
    foreach ($results as $i=>$result) {
        //$sum = $sum + intval($result->revenue);
        echo $result->representative;
        if ($i !== count($results) - 1) {
            echo ',';
        }

    }
    echo '<br>';
    foreach($results as $i=>$result) {
        //echo intval($result->revenue)/$sum*100;
        $result = (array) $result;
        echo $result['SUM(revenue)'];
        if ($i !== count($results) - 1) {
            echo ',';
        }
    }
    echo '<br>';
    die();
}


/**
 * Funnel for single representative ratios.
 */
else if (isset($_GET['funnel']) && $_GET['funnel'] === 'closeRatioByRep' && isset($_GET['rep'])) {
    $cyfeTable->select(array('revenue', 'close_date'));
    $cyfeTable->where('representative', $_GET['rep']);
    $results = $cyfeTable->get();

    $closed_count = 0;
    $open_count = 0;
    $closed_rev = 0;
    $open_rev = 0;

    foreach ($results as $result) {
        if ($result->close_date != '0000-00-00') {
            $closed_count++;
            $closed_rev = $closed_rev + $result->revenue;
        }
        else {
            $open_count++;
            $open_rev = $open_rev + $result->revenue;
        }
    }

    if (isset($_GET['show']) && $_GET['show'] === 'revenue') {
        echo 'Type,Count($)<br>';
        echo 'Open Leads,' . $open_rev . '<br>';
        echo 'Closed Leads,' . $closed_rev . '<br>';
        die();
    }
    echo 'Type,Count<br>';
    echo 'Open Leads,' . $open_count . '<br>';
    echo 'Closed Leads,' . $closed_count . '<br>';

    die();
}

/**
 * Gauge for company wide revenue.
 */
else if (isset($_GET['gauge']) && $_GET['gauge'] === 'revenue') {
    $cyfeTable->select(array('revenue', 'close_date'));
    $results = $cyfeTable->get();

    $closed_rev = 0;
    $total_rev = 0;

    foreach ($results as $result) {
        if ($result->close_date != '0000-00-00') {
            $closed_rev = $closed_rev + $result->revenue;
        }

        $total_rev = $total_rev + $result->revenue;
    }


    echo 'Revenue($),Total<br>';
    echo $closed_rev . ',' . $total_rev . '<br>';
    die();

}





/**
 * If it isnt one of the existing displays, lets see what we can do with
 * the parameters given.
 */
else {
    if (isset($_GET['select'])) {

        $cols = $cyfeTable->select($_GET['select']);
        // dump($cols, 'COLS');

    }

    //$cyfeTable->select(array('state', 'revenue', 'user_count', 'austins'));

    if (isset($_GET['group_by'])) {

        $group_by = $cyfeTable->group_by($_GET['group_by']);

        $key = array_search($_GET['group_by'], $cols);
        if ($key !== false && !is_null($group_by)) {
            unset($cols[$key]);
        }
    }
    //$cyfeTable->group_by('state');
    $results = $cyfeTable->get();

    /**
     * CYFE Pulling
     */

    if ( !is_null($group_by) ) {
        $group_by = str_replace('_', ' ', $group_by);
        echo ucwords($group_by) . ',';
    }

    foreach( $cols as $i=>$item_title) {
        $item_title = str_replace('_', ' ', $item_title);
        if ($i === count($cols) - 1) {
            echo ucwords($item_title);
        }
        else {
            echo ucwords($item_title) . ',';
        }
    }
    echo '<br>';

    foreach ($results as $obj) {

         if ( !is_null($group_by) ) {
             echo $obj->$group_by . ',';
         }

         foreach( $cols as $n=>$item) {
             if ($n === count($cols) - 1) {
                 echo $obj->$item;
             }
             else {
                 echo $obj->$item . ',';
             }
         }
         echo '<br>';

     }

     echo 'Color,#badcab,#cabbad';

     die();
}







// rand_populate_db(10000);
