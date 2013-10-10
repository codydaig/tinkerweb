<?php

/*
 * Blog Class
 * This file will ... 
 * 
 * @author Cody B. Daig
 * 
 * Last Modified: October 9th, 2013 
 */

// Include the Parent Framework Class
require_once("Framework.class.php");

class Blog extends Framework{
  public function __construct(){
    // Load the Website's Header
    parent::loadHeader();
    
    // Determine what is being requested
    if(URL == "/blog/")
    {
      // Load the Default Blog Page
      $this->listPosts();
    }
    
    // Load the Footer
    parent::loadFooter();
  }
  
  public function listPosts(){
    // Displays the List Events View
    echo "This is where blog posts will be listed.";
  }
}

?>