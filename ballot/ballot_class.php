<?php

class ballot_factory {
  public static function factory($election_type, $election_nid, $election_name) {
    switch ($election_type) {
      case 'majority':
        $ballot = new majority_vote_ballot($election_nid, $election_name);
        break;
      
      case 'instant_runoff':
        $ballot = new instant_runoff_vote_ballot($election_nid, $election_name);
        break;
        
      default:  // change to exception
        die('Unknown Election Type ' . $election_type );
    }
  
    if ($ballot instanceof Ballot) {
      return $ballot;
    }
    else {
      die('Unknown Error creating Ballot');
    }
  }
  
}

abstract class Ballot {
  private $election_nid;        // int
  private $title;               // string
  private $writein;             // boolean
  private $candidate_race_map;  // array
  private $races;               // array
  
  public function __construct($election_nid, $election_title) {
    $this->election_nid = $election_nid;
    $this->title = $election_title;
    $this->candidate_race_map = array();
  }
  
  public function get_title() {
    return $this->title;
  }
  
  public function add_race($race_nid, $race_title) {
    $this->races[$race_nid] = new ballot_race($race_nid, $race_title);
  }
  
  public function add_candidate($race_nid, $candidate_nid, $candidate_name) {
    assert( isset($this->races[$race_nid]));
    assert( array_key_exists($race_nid, $this->races));
    assert( !array_key_exists($candidate_nid, $this->candidate_race_map));
    
    $this->candidate_race_map[$candidate_nid] = $race_nid;    
    $this->races[$race_nid]->add_candidate($candidate_nid, $candidate_name);
  }
  
  public function set_candidate_bio($candidate_nid, $candidate_bio) {
    assert( array_key_exists($candidate_nid, $this->candidate_race_map));
    $race_nid = $this->candidate_race_map[$candidate_nid];
    
    assert( isset($this->races[$race_nid]));
    assert( array_key_exists($race_nid, $this->races));

    $this->races[$race_nid]->set_candidate_bio($candidate_nid, $candidate_bio);
  }
  
  public function get_candidate_bio($candidate_nid) {
    $race_nid = $this->candidate_race_map[$candidate_nid];
    return $this->races[$race_nid]->get_candidate_bio($candidate_nid);
  }
  
  public function get_race_nids() {
    return array_keys($this->races);
  }
  
  public function get_candidate_nids($race_nid) {
    return $this->races[$race_nid]->get_candidate_nids();
  }
  
  public function get_candidate_race_map() {
    return $this->candidate_race_map;
  }
  
  private function printArray($a) {
    echo '<pre>';
    print_r($a);
    echo '</pre>';
  }
  
}

interface Iballot {
  public function get_type();
  //public function add_candidate_group($race_nid, $candidate_nid, $group_names);
}


class instant_runoff_vote_ballot extends Ballot implements Iballot {

  public function __construct($election_nid, $title) {
    parent::__construct($election_nid, $title);
    $this->writein = false;
  }
  
  public function get_type() {
    return "irv";
  }
}

class majority_vote_ballot extends Ballot implements Iballot {
  
  public function __construct($election_nid, $title) {
    parent::__construct($election_nid, $title);
    $this->writein = false;
  }

  public function get_type() {
    return "majority";
  }
}

class ballot_race {
  private $race_nid;
  private $race_title;
  private $candidates;
  
  public function __contruct($race_nid, $race_title) {
    $this->race_nid = $race_nid;
    $this->race_title = $race_title;
    $this->candidates = array();
  }
  
  public function add_candidate($candidate_nid, $candidate_name) {
    assert(!isset($this->candidates[$candidate_nid]));
    //assert(!array_key_exists($candidate_nid, $this->candidates));
    
    $this->candidates[$candidate_nid] = new ballot_candidate($candidate_nid, $candidate_name);
  }
  
  public function set_candidate_bio($candidate_nid, $candidate_bio) {
    $this->candidates[$candidate_nid]->set_candidate_bio($candidate_bio);
  }
  
  public function get_candidate_bio($candidate_nid) {
    return $this->candidates[$candidate_nid]->get_candidate_bio();
  }
  
  public function get_candidate_nids() {
    //$this->printArray($this->candidates);
    return array_keys($this->candidates);
  }

  private function printArray($a) {
    echo '<p>race</p>';
    echo '<pre>';
    print_r($a);
    echo '</pre>';
  }
}

class ballot_candidate {
  private $candidate_name;
  private $candidate_nid;
  private $candidate_bio;
/*  
  private $candidate_group_names;
  private $party_name;
  private $party_nid;

  private $candidate_voting_value;
*/  
  public function __construct($candiate_nid, $candidate_name) {
    $this->candidate_nid  = $candiate_nid;
    $this->candidate_name = $candidate_name;
/*    
    $platform = NULL, $party_nid = NULL, $party_name = NULL, $party_platform = NULL
    $this->candidate_platform = $platform;
    
    $this->party_nid  = $party_nid;
    $this->party_name = $party_name;
        
    $this->candidate_voting_value = 0;
    $this->candidate_group_names = NULL;
*/
  }
  
  public function set_candidate_bio($candidate_bio) {
    $this->candidate_bio = $candidate_bio;
  }
  
  public function set_candidate_group ($names) {
    $this->candidate_group_names = $names;
  }
    
  public function set_voting_value($value) {
    $this->candidate_voting_value = $value;
  }
  
  public function get_candidate_bio() {
    return $this->candidate_bio;
  }
}

class view_factory {
  public static function factory($view_type, $ballot_model) {
    switch ($view_type) {
      case 'ballot':
        $ballot_view = new majority_view($ballot_model);
        break;
      
      case 'confirm':
        $ballot_view = new instant_runoff_view($ballot_model);
        break;
        
      default:
        die('Unknown View Type ' . $view_type );
    }
  
    if ($ballot_view instanceof View) {
      return $ballot_view;
    }
    else {
      die('Unknown Error creating Ballot');
    }
  }
  
}

abstract class View {
  private $ballot_model;

  public function __construct($ballot_model) {
    $this->ballot_model = $ballot_model;
  }

  public function print_ballot() {
   echo '<h3>'.$this->ballot_model->get_title().'</h3>'; 
}

}

class majority_view extends View {

  public function __construct($ballot_model) {
    parent::__construct($ballot_model);
  }
}

class instant_runoff_view extends View{

  public function __construct($ballot_model) {
    parent::__construct($ballot_model);
  }
}




?>
