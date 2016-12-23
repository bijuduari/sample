<?php

# Please keep one less than what is expected.
# If the rating eleigible count is 5, have it as 4.
# the code is written as count > rank_eligible_vote_count
$rank_eligible_vote_count = 0;


# Note: If the criteria name is changed, it should be updated here
# Because this is the Only place where we are hardcoiding the type name.

$weightage = array(
   'Demo' => array(
     'informative'     => 0.4,
     'innovative'      => 0.4,
     'presentability'  => 0.2 ),
  
   'Idea' => array(
     'informative'     => 0.3,
     'innovative'      => 0.4,
     'presentability'  => 0.3 ),
  
   'Creative' => array(
     'informative'     => 0.2,
     'innovative'      => 0.4,
     'presentability'  => 0.4 ),

   'Knowledge' => array(
     'informative'     => 0.3,
     'innovative'      => 0.4,
     'presentability'  => 0.3 )
);



?>
