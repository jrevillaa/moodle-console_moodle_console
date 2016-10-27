<?php 

$settings = null;

defined("MOODLE_INTERNAL") || die;

if ($hassiteconfig) {
  //Add link in modules
  $ADMIN->add("modules", new admin_category("categorydummy1", get_string("exampletitle","local_(?)")));
  
  //generate link to Admin
  $ADMIN->add("categorydummy1", new admin_externalpage("generateDummy1", 
                get_string("menutitleexample", "local_(?)"), 
                new moodle_url("/local/(?)/index.php")));

}