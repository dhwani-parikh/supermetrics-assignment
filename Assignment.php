<?php
require ( 'SuperMetrics.php' );
require ( 'Statistics.php' );

use apiassignment\supermetrics\SuperMetrics;
use apiassignment\statistics\Statistics;

$ini = parse_ini_file('config.ini');

$supermetric = new SuperMetrics($ini['client_id'],$ini['email'],$ini['name']);

$client = new Statistics($supermetric);
$token = $client->init();

//Get all posts
$allPosts = array();
for($i=1; $i<=10; $i++){
    $posts = (array)$client->listStatistics($i);
    if(isset($posts['data']->posts)){
        $allPosts = array_merge($allPosts,$posts['data']->posts);
    }else{
        $client->init();
    }

}

$monthArray = [];
$weekArray = [];
$userPostArray = [];
$lastPostInArray = 0;
$longestPostArray = [];

foreach ($allPosts as $post) {
    //Avg char length of posts per month => Get month with length of each post
    if(!isset($monthArray[ date("F Y",strtotime($post->created_time))])) {
        $monthArray[date("F Y", strtotime($post->created_time))] = [];
    }
    array_push($monthArray[date("F Y",strtotime($post->created_time))], strlen(($post->message)));

    //Longest post by char length per month
    if(!isset($longestPostArray[date("F Y",strtotime($post->created_time))])) {
        $longestPostArray[date("F Y",strtotime($post->created_time))] = $post;
    }elseif (strlen($post->message) > strlen($longestPostArray[date("F Y",strtotime($post->created_time))]->message)) {
        $longestPostArray[date("F Y", strtotime($post->created_time))] = $post;
    }

    //Total Post splits by week number
    if(!isset($weekArray[date("W",strtotime($post->created_time))])) {
        $weekArray[date("W", strtotime($post->created_time))] = 0;
    }
    $weekArray[date("W", strtotime($post->created_time))] = $weekArray[date("W", strtotime($post->created_time))] +1;

    //Average number of post per user per month
    if(!isset($userPostArray[ date("F Y",strtotime($post->created_time))][$post->from_id]))
        $userPostArray[date("F Y",strtotime($post->created_time))][$post->from_id] = 0;
    $userPostArray[date("F Y",strtotime($post->created_time))][$post->from_id]= $userPostArray[date("F Y",strtotime($post->created_time))][$post->from_id] +1;
}

//Avg char length of posts per month
$avgCharLenPostsArray = [];
foreach($monthArray as $month => $values)  {
    $avgCharLenPostsArray[$month]['count'] = count($values);
    $avgCharLenPostsArray[$month]['total'] = array_sum($values);
    $avgCharLenPostsArray[$month]['avg'] = round($avgCharLenPostsArray[$month]['total'] / $avgCharLenPostsArray[$month]['count'], 2);
}

//Average number of post per user per month
$avgPostsArray = [];
foreach($userPostArray as $userPost => $values)  {
    $avgPostsArray[$userPost]['avg'] = round(array_sum($values) / count($values), 2);
}
print "<pre>";
//Average character length of posts per month
print json_encode(['Avg. chars of post per month' => array_combine(array_keys($avgCharLenPostsArray), array_column($avgCharLenPostsArray, 'avg'))],JSON_PRETTY_PRINT);

//Longest post by char length per month
print json_encode(['Longest post per month' => $longestPostArray], JSON_PRETTY_PRINT);

//Total Post splits by week number
print json_encode(['Total posts per week' => $weekArray], JSON_PRETTY_PRINT);

//Average number of post per user per month
print json_encode(['Avg. posts per user per month' => array_combine(array_keys($avgPostsArray), array_column($avgPostsArray, 'avg'))], JSON_PRETTY_PRINT);

