<?php
require_once('simpletest/autorun.php');
require_once('simpletest/web_tester.php');
require_once('simpletest/unit_tester.php');

require_once 'ballot_class.php';


class ballot_class_test extends UnitTestCase
{ 
  public $election;
  
  public function setUp() {
    $this->election = ballot_factory::factory('majority', 34, 'test' );  
  }
  
  public function tearDown() {
    unset($this->election);  // not sure if needed
  }
 
  public function test_object_null() {
     $this->assertNotNull($this->election);
   }
 
  public function test_object_type() {
     $this->assertIsA($this->election, 'Ballot');
   }
 
  public function test_get_type() {  
    $this->assertEqual($this->election->get_type(), 'majority');
  }
  
  public function test_get_title() {  
    $this->assertEqual($this->election->get_title(), 'test');
  }
  
  public function test_add_race1() {
    $this->election->add_race(41, 'president');
    
    $expected = array(41);
    $this->assertEqual($expected, $this->election->get_race_nids() );
  }
  
  public function test_add_race2() {
    $this->election->add_race(41, 'president');
    $this->election->add_race(44, 'representative');
    
    $expected = array(41, 44);
    $actual = $this->election->get_race_nids();
    $this->assertEqual($expected, $actual);
  }
  
  public function test_add_candidate1() {
    $this->election->add_race(41, 'president');
    $this->election->add_candidate(41, 234, 'Charles Clark');
    
    $expected = array(234);
    $this->assertEqual($expected, $this->election->get_candidate_nids(41) );    
  }
  
  public function test_add_candidate2() {
    $this->election->add_race(41, 'president');
    $this->election->add_candidate(41, 234, 'Charles Clark');
    $this->election->add_candidate(41, 545, 'John Jones');
    
    $expected = array(234,545);
    $actual = $this->election->get_candidate_nids(41);
    $this->assertEqual($expected, $actual);
  }
 
  public function test_add_candidate_bio() {
    $this->election->add_race(41, 'president');
    $this->election->add_race(63, 'vice president');
    $this->election->add_candidate(41, 234, 'Charles Clark');
    $this->election->add_candidate(41, 545, 'John Jones');
    $this->election->add_candidate(63, 345, 'Alan Strom');
    
    $bio = '<p>Sdlkjwer asdflkjWE Qdflkj werWER qewRqwf sdfj</p>';
    $this->election->set_candidate_bio(234, $bio);
    
    //$this->printArray($this->election->get_candidate_race_map());
    $actual_bio = $this->election->get_candidate_bio(234);
    $this->assertEqual($bio, $actual_bio);
//    $this->assertEqual($expected, $actual  );
  }
  
  
  private function printArray($a) {
    echo '<pre>';
    print_r($a);
    echo '</pre>';
  }
  
}



