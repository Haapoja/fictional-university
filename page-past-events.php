<?php 

get_header();
page_banner(array(
    "title"=>"Past Events!",
    "subtitle"=>"Recap of our past events"
  ));

?>


<div class="container container--narrow page-section">
<?php
$today = date("Ymd");
$pastEvents = new WP_Query(array(
    "paged" => get_query_var("paged", 1),//this is needed for pagination to work. Gets the page number. 1 is there for security, if there is no page number, it goes to 1
    "post_type" => "event",
    "meta-key" => "event_date",  //the key for the orderby attribute.
    "orderby" => "meta_value_num", //meta= custom wordpress data, like custom fields. Needs a meta key for it to work
    "order"=> "ASC",
    "meta_query"=>array(
     array( //filters the events by date. wont display dates that are smaller than todays date
       "key" => "event_date",
       "compare" => "<",
       "value" => $today,
       "type" => "numeric"
     )
    )
));

while($pastEvents->have_posts()){
 $pastEvents -> the_post(); 
   get_template_part("template-parts/event");
 } //paginate links does not work out of the box with custom queries. It needs additional information about the query, like how many pages there will be
echo paginate_links(array(
    "total" => $pastEvents->max_num_pages,  //looks for the number of pages inside $pastEvents
   
));

?>



</div>

<?php get_footer();

?>