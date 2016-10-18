<?php 

$settings = null;

defined("MOODLE_INTERNAL") || die;

if ($hassiteconfig) {
  //Add link in modules
  $ADMIN->add("modules", new admin_category("categorydummy", get_string("exampletitle","local_holamundo")));
  
  //generate link to Admin
  $ADMIN->add("categorydummy", new admin_externalpage("generateDummy", 
                get_string("menutitleexample", "local_holamundo"), 
                new moodle_url("local/holamundo/index.php")));

}