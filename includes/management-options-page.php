<?php
/*
 * Copyright (C) 2014-2018 David D. Miller
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

function nscustom_plugin_menu()
{
    add_options_page('OA Election Management', 'OA Election Management', 'manage_options', 'nscustom', 'nscustom_options');
}
add_action('admin_menu', 'nscustom_plugin_menu');

function nscustom_options()
{

    global $wpdb;

    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    // =========================
    // form processing code here
    // =========================

    if (isset($_FILES['oa_unit_file'])) {
        if (preg_match('/\.xlsx$/', $_FILES['oa_unit_file']['name'])) {
            require_once plugin_dir_path(__FILE__) . '../vendor/autoload.php';

            $objReader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $objReader->setReadDataOnly(true);
            $objReader->setLoadSheetsOnly(array("All"));
            $objSpreadsheet = $objReader->load($_FILES["oa_unit_file"]["tmp_name"]);
            $objWorksheet = $objSpreadsheet->getActiveSheet();
            $columnMap = array(
            'Chapter'           => 'chapter_num', # REFERENCE!
            'District'          => 'district_num', # REFERENCE!
            'Unit Type'         => 'unit_type',
            'Unit Num.'         => 'unit_num',
            'Unit Des.'         => 'unit_desig',
            'City'              => 'unit_city',
            'State'             => 'unit_state',
            'County'            => 'unit_county',
            'Charter Org.'      => 'charter_org',
            'UL Full Name'      => 'ul_full_name',
            'UL Email Address'  => 'ul_email',
            'UL Phone Number'   => 'ul_phone_number',
            'OC Full Name'      => 'cc_full_name',
            'OC Email Address'  => 'cc_email',
            'OC Phone Number'   => 'cc_phone_number',
            'OAA Full Name'     => 'adv_full_name',
            'OAA Email Address' => 'adv_email',
            'OAA Phone Number'  => 'adv_phone_number',
            'OAR Full Name'     => 'rep_full_name',
            'OAR Email Address' => 'rep_email',
            'OAR Phone Number'  => 'rep_phone_number'
            );
            $editable_columns = [
            'unit_city',
            'unit_state',
            'unit_county',
            'charter_org',
            'ul_full_name',
            'ul_email',
            'ul_phone_number',
            'cc_full_name',
            'cc_email',
            'cc_phone_number',
            'adv_full_name',
            'adv_email',
            'adv_phone_number',
            'rep_full_name',
            'rep_email',
            'rep_phone_number'
            ];
            $complete = 0;
            $insertrecordcount = 0;
            $updaterecordcount = 0;
            $error_output = "";
            $districts = $wpdb->get_results("SELECT district_name, district_num FROM wp_oa_districts", OBJECT_K);
            $chapters = $wpdb->get_results("SELECT ChapterName, chapter_num FROM wp_oa_chapters", OBJECT_K);

            foreach ($objWorksheet->getRowIterator() as $row) {
                $rowData = array();
                if ($row->getRowIndex() == 1) {
                    # this is the header row, grab the headings
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    foreach ($cellIterator as $cell) {
                        $cellValue = $cell->getValue();
                        if (isset($columnMap[$cellValue])) {
                            $rowData[$columnMap[$cellValue]] = 1;
                            #echo "Found column " . htmlspecialchars($cell->getColumn()) . " with title '" . htmlspecialchars($cellValue) . "'<br>" . PHP_EOL;
                        } else {
                            #echo "Discarding unknown column " . htmlspecialchars($cell->getColumn()) . " with title '" . htmlspecialchars($cellValue) . "'<br>" . PHP_EOL;
                        }
                    }
                    $missingColumns = array();
                    foreach ($columnMap as $key => $value) {
                        if (!isset($rowData[$value])) {
                            $missingColumns[] = $key;
                        }
                    }
                    if ($missingColumns) {
                        ?><div class="error"><p><strong>Import failed.</strong></p><p>Missing required columns: <?php esc_html_e(implode(", ", $missingColumns)) ?></div><?php
                    $complete = 1; # Don't show "may have failed" box at the bottom
                    break;
                    } else {
                        #echo "<strong>Data format validated:</strong> Importing new data...<br>" . PHP_EOL;
                        # we just validated that we have a good data file, start handling data
                        $wpdb->show_errors();
                        ob_start();
                        # now we're ready for the incoming from the rest of the file.
                    }
                } else {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    foreach ($cellIterator as $cell) {
                        $columnName = $objWorksheet->getCell($cell->getColumn() . "1")->getValue();
                        $value = "";
                        if ($columnName === "Chapter") {
                            # the data will have a name, we need the foreign key reference ID
                            $chapter_name = $cell->getValue();
                            if (strpos($chapter_name, "-") !== false) {
                                $chapter_name = substr($chapter_name,0,1);
                            }
                            $chapter_row = $chapters[$chapter_name];
                            $value = $chapter_row->chapter_num;
                        } elseif ($columnName === "District") {
                            # the data will have a name, we need the foreign key reference ID
                            $district_name = $cell->getValue();
                            $district_row = $districts[$district_name];
                            $value = $district_row->district_num;
                        } else {
                            $value = $cell->getValue();
                        }
                        if (isset($columnMap[$columnName])) {
                            $rowData[$columnMap[$columnName]] = $value;
                        }
                    }
                    $existing = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_oa_units WHERE district_num = %s AND unit_type = %s AND unit_num = %s AND unit_desig = %s", $rowData['district_num'], $rowData['unit_type'], $rowData['unit_num'], $rowData['unit_desig']));
                    if ((null === $existing) && ($rowData['unit_desig'] == "BT")) {
                        # if the unit is designated Boy Troop but we didn't
                        # get a match, look it up again without a designator
                        # to since all troops use to be boy troops.
                        $existing = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_oa_units WHERE district_num = %s AND unit_type = %s AND unit_num = %s AND unit_desig = %s", $rowData['district_num'], $rowData['unit_type'], $rowData['unit_num'], ""));
                    }
                    if (null == $existing) {
                        # still didn't get a match, it's a new unit
                        $unit_desig = $rowData['unit_desig'];
                        if ($unit_desig == "" && $unit_desig !== "") { $rowData['unit_desig'] = ""; }
                        if ($rowData['unit_desig'] != "") { $unit_desig = "-" . $rowData['unit_desig']; }
                        echo "[+] Adding new unit: " . $district_name . " " . $rowData['unit_type'] . " " . $rowData['unit_num'] . $unit_desig . "\n";
                        if ($wpdb->insert("wp_oa_units", $rowData, array('%d','%d','%s','%d','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s'))) {
                            $insertrecordcount++;
                        }
                    } else {
                        # if we got here, there's an existing row for this troop. Check if it needs updating.
                        $updated = 0;
                        $unit_desig = $rowData['unit_desig'];
                        if ($rowData['unit_desig'] != "") { $unit_desig = "-" . $rowData['unit_desig']; }
                        if ($existing->unit_desig != $rowData['unit_desig']) {
                            # should only happen with "" -> "BT"
                            if ($updated == 0) {
                                echo "### processing existing unit: " . $district_name . " " . $rowData['unit_type'] . " " . $rowData['unit_num'] . $unit_desig . "\n";
                            }
                            echo "   => updating unit_desig from '' to 'BT'\n";
                            $wpdb->update('wp_oa_units', ['unit_desig' => $rowData['unit_desig']], ['id' => $existing->id], ["%s"], ["%d"]);
                            $updated++;
                        }
                        foreach ($editable_columns as $column) {
                            if ($existing->$column != $rowData[$column]) {
                                if ($updated == 0) {
                                    echo "### processing existing unit: " . $district_name . " " . $rowData['unit_type'] . " " . $rowData['unit_num'] . $unit_desig . "\n";
                                }
                                echo "   => updating " . $column . " from '" . $existing->$column . "' to '" . $rowData[$column] . "'\n";
                                $wpdb->update('wp_oa_units', [$column => $rowData[$column]], ['id' => $existing->id], ["%s"], ["%d"]);
                                $updated++;
                            }
                        }
                        if ($updated) { $updaterecordcount++; }
                    }
                }
            }
            $output = ob_get_clean();
            if (!$output) {
                ?><div class="error"><p><strong>Mysteriously no output?</strong></p></div><?php
            } else {
                ?><div class="updated"><p><strong>Read <?php esc_html_e($row->getRowIndex() - 2) ?> records from file.<br>
                Added <?php esc_html_e($insertrecordcount) ?> new units.<br>
                Updated <?php esc_html_e($updaterecordcount) ?> existing units.</strong></p>
            <p>Detail follows:</p>
                <pre><?php echo $output ?></pre>
            </div><?php
            }
        } else {
            ?><div class="error"><p><strong>Invalid file upload.</strong> Not an XLSX file.</p></div><?php
        }
    }

    // ============================
    // screens and forms start here
    // ============================

    //
    // MAIN SETTINGS SCREEN
    //

    echo '<div class="wrap">';

    // header

    echo "<h2>" . __('Update Troops', 'nslodge_custom') . "</h2>";

    // settings form

?>
<h3>Import unit data from OALM</h3>
<p>Export file from OALM Must use the <b>Units for export to website</b> view in the Units module.</p>
<form action="" method="post" enctype="multipart/form-data">
<label for="oalm_file">Click Browse, then select the xlsx file exported from OALM's grid export, then click "Upload":</label><br>
<input type="file" name="oa_unit_file" id="oa_unit_file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
<input type="submit" class="button button-primary" name="submit" value="Upload"><br>
</form>
<?php

    echo "</div>";
 // END OF SETTINGS SCREEN
}
