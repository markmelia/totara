<?php

require_once('../../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/local/dialogs/dialog_content_hierarchy.class.php');

require_once($CFG->dirroot.'/hierarchy/prefix/competency/lib.php');
require_once($CFG->dirroot.'/local/js/lib/setup.php');
require_once($CFG->dirroot.'/hierarchy/prefix/competency/related/lib.php');


///
/// Setup / loading data
///

// Competency id
$compid = required_param('id', PARAM_INT);

// Parent id
$parentid = optional_param('parentid', 0, PARAM_INT);

// Framework id
$frameworkid = optional_param('frameworkid', 0, PARAM_INT);

// Only return generated tree html
$treeonly = optional_param('treeonly', false, PARAM_BOOL);

// show search tab instead of browse
$search = optional_param('search', false, PARAM_BOOL);

// No javascript parameters
$nojs = optional_param('nojs', false, PARAM_BOOL);
$returnurl = optional_param('returnurl', '', PARAM_TEXT);
$s = optional_param('s', '', PARAM_TEXT);

// string of params needed in non-js url strings
$urlparams = 'id='.$compid.'&amp;frameworkid='.$frameworkid.'&amp;nojs='.$nojs.'&amp;returnurl='.urlencode($returnurl).'&amp;s='.$s;

// Setup page
admin_externalpage_setup('competencymanage', '', array(), '', $CFG->wwwroot.'/competency/related/add.php');

$alreadyrelated = comp_relation_get_relations($compid);
$alreadyselected = $alreadyrelated ? get_records_select('comp', 'id IN ('.implode(',', $alreadyrelated).')',
                                                        '', 'id, fullname') : array();
$alreadyrelated[$compid] = $compid;

///
/// Display page
///


if(!$nojs) {
    // Load dialog content generator
    $dialog = new totara_dialog_content_hierarchy_multi('competency', $frameworkid);

    // Toggle treeview only display
    $dialog->show_treeview_only = $treeonly;

    // Load items to display
    $dialog->load_items($parentid);

    // Set disabled/selected items
    $dialog->disabled_items = $alreadyrelated;
    $dialog->selected_items = $alreadyselected;

    // Set title
    $dialog->selected_title = 'selectedcompetencies';

    // Display
    echo $dialog->generate_markup();

} else {
    // none JS version of page
    // Check permissions
    $sitecontext = get_context_instance(CONTEXT_SYSTEM);
    require_capability('moodle/local:updatecompetency', $sitecontext);

    // Setup hierarchy object
    $hierarchy = new competency();

    // Load framework
    if (!$framework = $hierarchy->get_framework($frameworkid)) {
        error('Competency framework could not be found');
    }

    // Load competencies to display
    $competencies = $hierarchy->get_items_by_parent($parentid);

    admin_externalpage_print_header();
    echo '<h2>'.get_string('assignrelatedcompetencies', $hierarchy->prefix).'</h2>';

    echo '<p><a href="'.$returnurl.'">'.get_string('cancelwithoutassigning','hierarchy').'</a></p>';

    if(empty($frameworkid) || $frameworkid == 0) {

        echo build_nojs_frameworkpicker(
            $hierarchy,
            $CFG->wwwroot.'/hierarchy/prefix/competency/related/find.php',
            array(
                'returnurl' => $returnurl,
                's' => $s,
                'nojs' => 1,
                'id' => $compid,
            )
        );

    } else {
        ?>
<div id="nojsinstructions">
<?php
        echo build_nojs_breadcrumbs($hierarchy,
            $parentid,
            $CFG->wwwroot.'/hierarchy/prefix/competency/related/find.php',
            array(
                'id' => $compid,
                'returnurl' => $returnurl,
                's' => $s,
                'nojs' => $nojs,
                'frameworkid' => $frameworkid,
            )
        );

?>
<p>
<?php echo  get_string('clicktoassign', $hierarchy->prefix).' '.
            get_string('clicktoviewchildren', $hierarchy->prefix) ?>
</p>
</div>
<div class="nojsselect">
<?php
         echo build_nojs_treeview(
            $competencies,
            get_string('nochildcompetenciesfound', 'competency'),
            $CFG->wwwroot.'/hierarchy/prefix/competency/related/save.php',
            array(
                's' => $s,
                'returnurl' => $returnurl,
                'nojs' => 1,
                'frameworkid' => $frameworkid,
                'id' => $compid,
            ),
            $CFG->wwwroot.'/hierarchy/prefix/competency/related/find.php?'.$urlparams,
            $hierarchy->get_all_parents(),
            $alreadyrelated
        );

?>
</div>
<?php
    }

    print_footer();
}



