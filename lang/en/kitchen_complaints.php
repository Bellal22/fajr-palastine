<?php

return [
    'plural' => 'Kitchen Complaints',
    'singular' => 'Kitchen Complaint',
    'permission' => 'Follow up Kitchen Complaints',
    'actions' => [
        'list' => 'List Kitchen Complaints',
        'show' => 'Complaint Details',
        'respond' => 'Respond to Complaint',
        'delete' => 'Delete Complaint',
    ],
    'attributes' => [
        'camp_name' => 'Camp Name',
        'card_number' => 'Card Number',
        'complainant_name' => 'Complainant Name',
        'mobile_number' => 'Contact Mobile Number',
        'complaint_title' => 'Complaint Title',
        'complaint_text' => 'Complaint Text',
        'status' => 'Status',
        'response' => 'Response',
        'responded_at' => 'Responded At',
        'responded_by' => 'Responded By',
    ],
    'status' => [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'resolved' => 'Resolved',
        'rejected' => 'Rejected',
    ],
    'messages' => [
        'responded' => 'Response sent successfully.',
        'deleted' => 'Complaint deleted successfully.',
    ],
];
