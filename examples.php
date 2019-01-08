<?php

require_once './Assembla.php';

use MugoWeb\Assembla;

// create a personal API key and secret at https://app.assembla.com/user/edit/manage_clients
$assembla = new Assembla( "", "" );

/*
 *
 *
 * USAGE EXAMPLES
 *
 *
 */

// get the user data associated with the apikey and secret (useful for api calls that require a user id)
$currentUser = $assembla->getCurrentUser( );

$currentUserId = json_decode( $currentUser )->id;

// get a list of all spaces accessible to the current user (useful for api calls that require a space id)
$spaces = $assembla->getSpaces( );

$mugoPracticeSpaceId = "";

foreach (json_decode( $spaces ) as $space ) {
    if ( $space->name == "Mugo Practice" ) {
        $mugoPracticeSpaceId = $space-> id;
    }
}

// get a list of all milestones associated with the Mugo Practice space
$mugoPracticeMilestones = $assembla->getMilestones( $mugoPracticeSpaceId );

$mugoPracticeTestMilestoneId = "";

// check if a milestone by the name of "TEST" exists, if it does, note its id
foreach (json_decode( $mugoPracticeMilestones ) as $milestone ) {
    if ( $milestone->title == "TEST" ) {
        $mugoPracticeTestMilestoneId = $milestone-> id;
    }
}

// if the "TEST" milestone doesn't exist, create it and note the id
if (!$mugoPracticeTestMilestoneId) {
    // create a new milestone in the Mugo Practice space
    $createMilestone = $assembla->createMilestone( $mugoPracticeSpaceId,
        '{
            "milestone": {
                "title": "TEST"
            }
        }'
    );
    if ( json_decode($createMilestone)->id ) {
        $mugoPracticeTestMilestoneId = json_decode($createMilestone)->id;
    }
}

// update the "TEST" milestone
$updateMilestone = $assembla->updateMilestone( $mugoPracticeSpaceId, $mugoPracticeTestMilestoneId,
    '{
        "milestone": {
            "title": "TEST",
            "start_date": "2019-01-01T00:00:00Z",
            "due_date": "2019-01-30T00:00:00Z",
            "user_id":"' . $currentUserId . '"
        }
    }'
);

// delete the "TEST" milestone
$updateMilestone = $assembla->deleteMilestone( $mugoPracticeSpaceId, $mugoPracticeTestMilestoneId );

