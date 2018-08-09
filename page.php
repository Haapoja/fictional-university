<?php 

get_header();

while(have_posts()){
  the_post(); 
  page_banner(array(
    "title" => "something",
    "subtitle" => "subtitle",
  
  ));
  ?>



  <div class="container container--narrow page-section">

  <?php 
$ParentID = wp_get_post_parent_id(get_the_ID()); // equals the ID of the current parents page

         if($ParentID){ ?> <!--any positive number returns true. If a page does not have a parent element, it returns a 0, returnin a false -->
             
            
            <div class="metabox metabox--position-up metabox--with-home-link">
            <p><a class="metabox__blog-home-link" href="<?php the_permalink($ParentID); ?>"><i class="fa fa-home" aria-hidden="true"></i> Back to <?php echo get_the_title($ParentID);  ?></a> <span class="metabox__main"><?php the_title(); ?></span></p>
          </div>
     <?php    }
  

?>

   
    <?php 
    $testArray = get_pages(array( //if a page has children, get_pages will return them, if a page does not have children, the return value is null/0/false
        "child_of" => get_the_ID()
    ));
    if($ParentID or $testArray){ ?> <!-- this if statement shows the child page nav bar only if $ParentID=page is a parent or $testArray=page has children -->

    
    
    <div class="page-links">
      <h2 class="page-links__title"><a href="<?php the_permalink($ParentID) ?>"><?php echo get_the_title($ParentID);  ?></a></h2>
      <ul class="min-list">
        <?php 
        if($ParentID){ //only if the parent id = 0, dont do anything/ in other words if there is no parent page
             $findChild = $ParentID;
        } else {
            $findChild = get_the_ID();
        }
         wp_list_pages(array(
          "title_li" => NULL,
          "child_of" => $findChild
         ));

       ?>
      </ul>
    </div>
        <?php } ?>
    
    <div class="generic-content">
     <?php the_content(); ?>
    </div>

  </div>
  
<?php }

get_footer();

?>