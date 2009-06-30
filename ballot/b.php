<?php
require_once 'ballot_class.php';

$ballot_model = ballot_factory::factory('majority', 34, 'test' );  
//$ballot_view = view_factory::factory('ballot', $ballot_model);
$ballot_view = new majority_view($ballot_model);

$ballot_model->add_race(41, 'president');
$ballot_model->add_race(44, 'representative');
$ballot_model->add_race(63, 'vice president');

$ballot_model->add_candidate(41, 234, 'Charles Clark');
$ballot_model->add_candidate(41, 545, 'John Jones');
$ballot_model->add_candidate(63, 345, 'Alan Strom');
    
$bio = '<p>Sdlkjwer asdflkjWE Qdflkj werWER qewRqwf sdfj</p>';
$ballot_model->set_candidate_bio(234, $bio);

echo $ballot_view->print_ballot();


function printArray($a) {
  echo '<pre>';
  print_r($a);
  echo '</pre>';
}
  




