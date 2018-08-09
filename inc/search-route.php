
<?php  //the custom rest api starts here

add_action("rest_api_init", "universitySearch");

function universitySearch (){
    register_rest_route("university/v1","search",array(
    "methods" => WP_REST_SERVER::READABLE,
    "callback" => "universitySearchResults"
    ));
}

function universitySearchResults($data){
  $mainQuery = new WP_Query(array(
      "post_type" => array("post", "page", "professor", "program", "event", "campus"),
      "s" => sanitize_text_field($data["term"]) //this wp function stops anyone from injecting malicious code into the site
  ));
 
  $results = array(
      "generalInfo" => array(),
      "professors" => array(),
      "programs" => array(),
      "events" => array(),
      "campuses" =>array()
  );

while($mainQuery->have_posts()){
$mainQuery->the_post();

if (get_post_type() == "post" or get_post_type() == "page"){
    array_push($results["generalInfo"], array(
        "title" => get_the_title(),
        "url" => get_the_permalink(),
        "postType"=>get_post_type(),
        "author"=>get_the_author()
    ));
}
if (get_post_type() == "professor"){
    array_push($results["professors"], array(
        "title" => get_the_title(),
        "url" => get_the_permalink(),
        "image"=>get_the_post_thumbnail_url(0, "proff")
    ));
}
if (get_post_type() == "program"){
    array_push($results["programs"], array(
        "title" => get_the_title(),
        "url" => get_the_permalink(),
        "id"=> get_the_id()
    ));
}
if (get_post_type() == "event" ){
    $eventDate = new DateTime(get_field("event_date"));

    $desc = null;
    if (has_excerpt()){
     $desc = get_the_excerpt();
  }else{
    $desc = wp_trim_words(get_the_content(), 15);
  }

    array_push($results["events"], array(
        "title" => get_the_title(),
        "url" => get_the_permalink(),
        "month" =>$eventDate -> format("M"),
        "day" =>$eventDate -> format("j"),
        "desc" => $desc
    ));
}
if (get_post_type() == "campus"){
    array_push($results["campuses"], array(
        "title" => get_the_title(),
        "url" => get_the_permalink()
    ));
}

}

//custom query for handling program/professor relationships
$programsMetaQuery = array("relation" => "OR");

foreach ($results["programs"] as $item){
 array_push($programsMetaQuery,  array(
    "key"=> "related_programs",    
    "compare"=>"LIKE",  
    "value"=>'"' . $item["id"] . '"'
));
}

$programRelationship = new WP_Query(array(
 "post_type" => "professor",
 "meta_query" => $programsMetaQuery 
));
while($programRelationship->have_posts()){
    $programRelationship->the_post();

    if (get_post_type() == "professor"){
        array_push($results["professors"], array(
            "title" => get_the_title(),
            "url" => get_the_permalink(),
            "image"=>get_the_post_thumbnail_url(0, "proff")
        ));
    }
}
//array_unique removes duplicates, array_values removes the numeric keycode that array_unique adds to arrays
$results["professors"] = array_values(array_unique($results["professors"], SORT_REGULAR));

return $results;

}

