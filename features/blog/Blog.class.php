<?php
/*
 * Blog Class
 * This file will handle anything to do with the blog Frontend and Backend!! 
 * 
 * @author Cody B. Daig
 * 
 * Last Modified: October 21th, 2013 
 */

// Include the Parent Framework Class
require_once("../classes/Framework.class.php");

class Blog extends Framework {

    public $name = "Blog";

    public function __construct() {
        if (BACKEND_ENABLED == true && parent::isUserLoggedIn()) {
            $this->constructBackend();
        } else {
            // Load the Website's Header
            parent::loadHeader();

            // Determine what is being requested
            if (URL == "/blog/") {
                // Load the Default Blog Page
                $this->listPosts();
            } else {
                // Whoa... Something hapened
                echo "Ths page you requested is not a valid page.";
            }

            // Load the Footer
            parent::loadFooter();
        }
    }

    public function listPosts() {
        $post_query = $this->getPosts();

        echo "<h1>Blog</h1>";

        for ($b = 0; $b < mysql_num_rows($post_query); $b++) {
            ?>

            <div class="blog_post">
                <div class="blog_title"><h2><a href="post/?id=<?php echo mysql_result($post_query, $b, "ID") ?>"><?php echo mysql_result($post_query, $b, "Title") ?></a>
                        <br /><small><?php echo "by: " . mysql_result($post_query, $b, "Firstname") . " on: " . date("l, F d, Y", strtotime(mysql_result($post_query, $b, "DateTime"))) . " ~ filed under: " . mysql_result($post_query, $b, "Name") ?></small></h2></div>
                <p><?php echo mysql_result($post_query, $b, "Content") ?></p>
                <div class="blog_info">-- <a href="post/?id=<?php echo mysql_result($post_query, $b, "ID") ?>">Comments</a> --</div>
            </div>
            <?php
        }
    }
    
    public function backendListPosts(){
        $post_query = $this->backendGetPosts();
        
    }
    
    public function backendGetPosts() {
        $sql = "SELECT blog_posts.*, users.Firstname, users.Lastname, blog_categories.Name  FROM blog_posts INNER JOIN users ON blog_posts.Author=users.ID INNER JOIN blog_categories ON blog_posts.Category=blog_categories.ID ORDER BY DateTime DESC LIMIT 0 , 30";
        $post_query = mysql_query($sql);
        return $post_query;
    }

    public function getPosts() {
        $sql = "SELECT blog_posts.*, users.Firstname, users.Lastname, blog_categories.Name  FROM blog_posts INNER JOIN users ON blog_posts.Author=users.ID INNER JOIN blog_categories ON blog_posts.Category=blog_categories.ID WHERE Status='Published' ORDER BY DateTime DESC LIMIT 0 , 30";
        $post_query = mysql_query($sql);
        return $post_query;
    }

    public function getCategories() {

        $sql = "SELECT * FROM blog_categories";
        $cat_query = mysql_query($sql);
        return $cat_query;
    }

    public function constructBackend() {
        GLOBAL $user;
        // GLOBAL $name;
        $id = $user["ID"];
        $url = str_replace("/" . BACKEND, "", URL);
        $url2 = explode("/", $url);

        // Load the Backend Header
        include_once("../globals/layout/backend_header.php");

        print parent::featureHasPermission($id, $name, "Admin");

        // Determine Requested Page
        if ($url == "/blog/") {
            if (parent::featureHasPermission($id, $this->name, "Admin")) {
                // Load the Default Page
                echo "<h1>Congrats! Your a Blog Admin.</h1>";
            }
        } else if ($url == "/blog/new/") {
            if (parent::featureHasPermission($id, $this->name, "NewPost")) {
                echo "<h1>Create a New Blog Post</h1>";
                $this->postForm(0);
            }
        } else if ($url2[1] == "blog" && $url2[2] == "edit") {
            if (parent::featureHasPermission($id, $this->name, "NewPost")) {
                echo "<h1>Edit a Blog Post</h1>";
                $this->postForm($url2[3]);
            }
        } else if (parent::featureHasPermission($id, $name, "%")) {
            echo "Whoa.... Page Not Found.....";
        } else {
            parent::forceHome();
        }
    }

    public function submitPost() {
        $sql = "INSERT INTO `blog_posts` SET `Title`='" . $_POST["Title"] . "', `Slug`='" . $_POST["Slug"] . "', `Author`='" . $_POST["Author"] . "', `Category`='" . $_POST["Category"] . "', `DateTime`='" . $_POST["DateTime"] . "', `Status`='" . $_POST["Status"] . "', `Content`='" . $_POST["Content"] . "'";
        mysql_query($sql);
        if (!mysql_error()) {
            echo '<div class="panel panel-success"><div class="panel-heading">The post has been created successfully!</div></div>';
        } else {
            echo '<div class="panel panel-danger">Something Occured. Please check the form again.</div>';
        }
        return mysql_insert_id();
    }

    public function updatePost() {
        $sql = "UPDATE `blog_posts` SET `Title`='" . $_POST["Title"] . "', `Slug`='" . $_POST["Slug"] . "', `Author`='" . $_POST["Author"] . "', `Category`='" . $_POST["Category"] . "', `DateTime`='" . $_POST["DateTime"] . "', `Status`='" . $_POST["Status"] . "', `Content`='" . $_POST["Content"] . "' WHERE `ID`='" . $_POST["id"] . "'";
        mysql_query($sql);
        if (!mysql_error()) {
            echo '<div class="panel panel-success"><div class="panel-heading">The post has been updated successfully!</div></div>';
        } else {
            echo '<div class="panel panel-danger">Something Occured. Please check the form again.</div>';
        }
        return mysql_insert_id();
    }

    public function postForm($id) {
        if (isset($_POST["id"])) {
            if ($_POST["id"] == "NEW") {
                $id = $this->submitPost();
            } else {
                $this->updatePost();
                $id = $_POST["id"];
            }
        }
        if ($id > 0) {
            $sql = "SELECT blog_posts.*, users.Firstname, users.Lastname, blog_categories.Name  FROM blog_posts INNER JOIN users ON blog_posts.Author=users.ID INNER JOIN blog_categories ON blog_posts.Category=blog_categories.ID WHERE blog_posts.ID='" . $id . "'";
            $post_query = mysql_query($sql);
            $post = mysql_fetch_array($post_query);
        }
        echo '<form action="" method="post" class="class="form-horizontal" role="form">';
        echo '<div class="form-group">';
        echo '<label class="col-lg-2 control-label" for="Title">Post Title</label>';
        echo '<input type="text" class="form-control" id="Title" name="Title" placeholder="Post Title" value="' . $post["Title"] . '">';
        echo '</div>';
        echo '<div class="form-group">';
        echo '<label class="col-lg-2 control-label" for="exampleInputEmail2">Slug</label>';
        echo '<input type="text" class="form-control" id="exampleInputEmail2" name="Slug" value="' . $post["Slug"] . '" placeholder="slug">';
        echo '</div>';
        echo '<div class="form-group">';
        echo '<label class="col-lg-2 control-label" for="exampleInputEmail2">Author</label>';
        echo '<input type="text" class="form-control" id="exampleInputEmail2" name="Author" value="' . $post["Firstname"] . ' ' . $post["Lastname"] . '" placeholder="Author">';
        echo '</div>';
        echo '<div class="form-group">';
        echo '<label class="col-lg-2 control-label" for="exampleInputEmail2">Category</label>';
        echo '<select class="form-control" name="Category">';
        $categories = $this->getCategories();
        for ($i = 0; $i < mysql_num_rows($categories); $i++) {
            echo '<option value="' . mysql_result($categories, $i, "ID") . '"';
            if ($post["Category"] == mysql_result($categories, $i, "ID")) {
                echo ' selected';
            }
            echo '>' . mysql_result($categories, $i, "Name") . '</option>';
        }
        echo '</select>';
        echo '</div>';
        echo '<div class="form-group">';
        echo '<label class="col-lg-2 control-label" for="exampleInputEmail2">DateTime</label>';
        echo '<input type="text" class="form-control" id="exampleInputEmail2" name="DateTime" value="' . $post["DateTime"] . '" placeholder="DateTime">';
        echo '</div>';
        echo '<div class="form-group">';
        echo '<label class="col-lg-2 control-label" for="exampleInputEmail2">Status</label>';
        echo '<select class="form-control" name="Status"><option value="Draft"';
        if ($post["Status"] == "Draft") {
            echo ' selected';
        }
        echo '>Draft</option><option value="Waiting Approval"';
        if ($post["Status"] == "Waiting Approval") {
            echo ' selected';
        }
        echo '>Waiting Approval</option><option value="Published"';
        if ($post["Status"] == "Published") {
            echo ' selected';
        }
        echo '>Published</option><option value="Archived"';
        if ($post["Status"] == "Archived") {
            echo ' selected';
        }
        echo '>Archived</option></select>';
        echo '</div>';
        echo '<div class="form-group">';
        echo '<label class="col-lg-2 control-label" for="exampleInputEmail2">Content</label>';
        echo '<input type="text" class="form-control" id="exampleInputEmail2" name="Content" value="' . $post["Content"] . '" placeholder="Content">';
        echo '</div>';
        echo '<button type="submit" class="btn btn-default">Submit</button>';
        if ($id > 0) {
            echo '<input type="hidden" name="id" value="' . $id . '" />';
        } else {
            echo '<input type="hidden" name="id" value="NEW" />';
        }
        echo '</form>';
    }

    public function backendDefaultPage() {
        parent::loadBackendHeader();
    }

}
?>
