<?php

require get_theme_file_path("/inc/search-route.php");

function university_custom_rest(){
  register_rest_field("post","authorName", array(
    "get_callback" => function(){return get_the_author();}
  ));
}

add_action("rest_api_init", "university_custom_rest");

function Page_Banner($args = NULL){  //makes the argument optional instead of required
    
    if(!$args["title"]){ //fallback incase a page does not have a custom title
      $args["title"] = get_the_title();
    }

    if(!$args["subtitle"]){//fallback for subtitle
      $args["subtitle"] = get_field("page_banner_text");
    }

    if(!$args["photo"]){
       if(get_field("page_banner_background_image")){
          $args["photo"] = get_field("page_banner_background_image")["sizes"]["pageBanner"];
       }else{
           $args["photo"] = get_theme_file_uri("/images/ocean.jpg");
       }
    }

   ?>
    <div class="page-banner">
    <div class="page-banner__bg-image" style="background-image: url(<?php echo $args["photo"];  ?>);"></div>
    <div class="page-banner__content container container--narrow"> 
    <h1 class="page-banner__title"><?php echo $args["title"] ?></h1>
      <div class="page-banner__intro">
        <p><?php echo $args["subtitle"] ?></p>
      </div>
    </div>  
  </div>



 <?php }


function action_function(){
  wp_enqueue_script("map","//maps.googleapis.com/maps/api/js?key=AIzaSyDb_1n4T7gn_fp72p62FFDq7FUyjJx06LY", NULL, "1.0",true);
   wp_enqueue_script("slide", get_theme_file_uri("/js/scripts-bundled.js"), NULL, microtime(),true);
    wp_localize_script("slide","universityData",array(
      "root_url" => get_site_url()

    ));
    wp_enqueue_style("fa-awesome", "//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
    wp_enqueue_style("css", get_stylesheet_uri(), NULL, microtime());
    wp_enqueue_style("fonts", "//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i");

}

add_action("wp_enqueue_scripts", "action_function");

function university_features(){  
    add_theme_support("title-tag"); //this displays the page title on the tab
   
    register_nav_menu("headerMenuLocation", "Header Menu Location");
    register_nav_menu("footerMenuLocation", "Footer Menu Location");
    register_nav_menu("footerMenuLocation1", "Footer Menu Location one");
    
}

add_action("after_setup_theme", "university_features");


function image_hell(){
    add_theme_support("post-thumbnails");
    add_image_size("pageBanner", 1500, 350, true);
    add_image_size("proff", 400, 260, true);
    add_image_size("proff1", 480, 650, true);
}
add_action("after_setup_theme", "image_hell");

function university_adjust_queries($query){ 
  
  
  
  
  if(!is_admin() AND is_post_type_archive("campus") AND $query->is_main_query()){  //evaluates to true on the frontend of the site
       $query->set("posts_per_page", -1);
    }                                         
  
  
  //if the query is not specified, this will affect EVERY query in wordpress, it will even affect the dashboard
//handles the display order of events on the program archive page
if(!is_admin() AND is_post_type_archive("program") AND $query->is_main_query()){  //evaluates to true on the frontend of the site
$query->set("orderby", "title");
$query->set("order", "ASC");
$query->set("posts_per_page", -1);
}

//handles the display order of events on the event archive page
  if(!is_admin() AND is_post_type_archive("event") AND $query->is_main_query()){                    //! negates the function. In other words: if not on admin dashboard, run the code
   
    $today = date("Ymd");
    $query->set("meta_key", "event_date");
    $query->set("orderby", "meta_value_num");
    $query->set("order", "ASC");
    $query->set("meta_query", array(
        array(
          "key" => "event_date",
          "compare" => ">=",
          "value" => $today,
          "type" => "numeric"
        )
       ));
}
}

add_action("pre_get_posts", "university_adjust_queries");


function map_key($api){
$api["key"] = "AIzaSyDb_1n4T7gn_fp72p62FFDq7FUyjJx06LY";
return $api;
}

add_filter("acf/fields/google_map/api", "map_key");
