<?php
// -----
// Common Module (used by many of my plugins) to provide downward compatibility to Zen Cart v1.5.5 admin-level changes.  Created by lat9 (http://vinosdefrutastropicales.com)
// Copyright (c) 2015-2016, Vinos de Frutas Tropicales
//
if (!defined('IS_ADMIN_FLAG') || IS_ADMIN_FLAG !== true) {
    die('Illegal Access');
}
// -----
// Check to see if either of the functions, introduced in Zen Cart v1.5.5, are missing; if so, define the associated function using
// its code -- pulled from /admin/includes/functions/general.php.
//
if (!function_exists ('zen_build_subdirectories_array')) {
/**
   * build a list of directories in a specified parent folder
   * (formatted in id/text pairs for SELECT boxes)
   *
   * @todo convert to a directory-iterator instead
   * @todo - this will be deprecated after converting remaining admin pages to LEAD format
   *
   * @return array (id/text pairs)
   */
  function zen_build_subdirectories_array($parent_folder = '', $default_text = 'Main Directory') {
    if ($parent_folder == '') $parent_folder = DIR_FS_CATALOG_IMAGES;
    $dir_info[] = array('id' => '', 'text' => $default_text);

    $dir = @dir($parent_folder);
    while ($file = $dir->read()) {
      if (is_dir($parent_folder . $file) && $file != "." && $file != "..") {
        $dir_info[] = array('id' => $file . '/', 'text' => $file);
      }
    }
    $dir->close();
    sort($dir_info);
    return $dir_info;
  }
  
}

if (!function_exists ('zen_sort_array')) {
  /**
   * Perform an array multisort, based on 1 or 2 columns being passed
   * (defaults to sorting by first column ascendingly then second column ascendingly unless otherwise specified)
   *
   * @param $data        multidimensional array to be sorted
   * @param $columnName1 string representing the named column to sort by as first criteria
   * @param $order1      either SORT_ASC or SORT_DESC (default SORT_ASC)
   * @param $columnName2 string representing named column as second criteria
   * @param $order2      either SORT_ASC or SORT_DESC (default SORT_ASC)
   * @return array   Original array sorted as specified
   */
  function zen_sort_array($data, $columnName1 = '', $order1 = SORT_ASC, $columnName2 = '', $order2 = SORT_ASC)
  {
    // simple validations
    $keys = array_keys($data);
    if ($columnName1 == '') {
      $columnName1 = $keys[0];
    }
    if (!in_array($order1, array(SORT_ASC, SORT_DESC))) $order1=SORT_ASC;
    if ($columnName2 == '') {
      $columnName2 = $keys[1];
    }
    if (!in_array($order2, array(SORT_ASC, SORT_DESC))) $order2=SORT_ASC;

    // prepare sub-arrays for aiding in sorting
    foreach($data as $key=>$val)
    {
      $sort1[] = $val[$columnName1];
      $sort2[] = $val[$columnName2];
    }
    // do actual sort based on specified fields.
    array_multisort($sort1, $order1, $sort2, $order2, $data);
    return $data;
  }

}

if (!function_exists ('is__writeable')) {
  /**
   * function to override PHP's is_writable() which can occasionally be unreliable due to O/S and F/S differences
   * attempts to open the specified file for writing. Returns true if successful, false if not.
   * if a directory is specified, uses PHP's is_writable() anyway
   *
   * @var string
   * @return boolean
   */
  function is__writeable($filepath, $make_unwritable = true) {
    if (is_dir($filepath)) return is_writable($filepath);
    $fp = @fopen($filepath, 'a');
    if ($fp) {
      @fclose($fp);
      if ($make_unwritable) set_unwritable($filepath);
      $fp = @fopen($filepath, 'a');
      if ($fp) {
        @fclose($fp);
        return true;
      }
    }
    return false;
  }
  
}

if (!function_exists ('set_unwriteable')) {
  /**
   * attempts to make the specified file read-only
   *
   * @var string
   * @return boolean
   */
  function set_unwritable($filepath) {
    return @chmod($filepath, 0444);
  }
  
}