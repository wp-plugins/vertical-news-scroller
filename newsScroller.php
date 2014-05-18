<?php
    /* 
    Plugin Name:Vertical News Scroller
    Plugin URI:http://www.i13websolution.com
    Description:Plugin for scrolling Vertical News on wordpress site.Admin can add any number of news.
    Author:I Thirteen Web Solution
    Author URI: http://www.i13websolution.com
    Version:1.3
    */

    error_reporting(0);
    add_action( 'admin_init', 'vertical_news_scroller_plugin_admin_init' );
    register_activation_hook(__FILE__,'install_newsscroller');

    add_action('admin_menu',    'scrollnews_plugin_menu');  
    /* Add our function to the widgets_init hook. */
    add_action( 'widgets_init', 'verticalScrollSet' );

    function vertical_news_scroller_plugin_admin_init(){

        $url = plugin_dir_url(__FILE__);  
        wp_enqueue_script('jquery'); 
        wp_enqueue_script( 'jquery.validate', $url.'js/jquery.validate.js' );  


    }

    function install_newsscroller(){

        global $wpdb;
        $table_name = $wpdb->prefix . "scroll_news";

        $sql = "CREATE TABLE " . $table_name . " (
        id int(10) unsigned NOT NULL auto_increment,
        title varchar(1000) NOT NULL,
        content varchar(2000) NOT NULL,
        createdon datetime NOT NULL,
        custom_link varchar(1000) default NULL,
        PRIMARY KEY  (id)
        );";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);


    } 


    function scrollnews_plugin_menu(){

        add_menu_page(__('Scroll news'), __("Manage Scrolling News"), 'administrator', 'Scrollnews-settings','managenews');

    }


    /* Function that registers our widget. */
    function verticalScrollSet() {
        register_widget( 'verticalScroll' );
    }


    function managenews(){

        $action='gridview';
        global $wpdb;


        if(isset($_GET['action']) and $_GET['action']!=''){


            $action=trim($_GET['action']);
        }

    ?>
    <!--[if !IE]><!-->
    <style type="text/css">

        @media only screen and (max-width: 800px) {

            /* Force table to not be like tables anymore */
            #no-more-tables table, 
            #no-more-tables thead, 
            #no-more-tables tbody, 
            #no-more-tables th, 
            #no-more-tables td, 
            #no-more-tables tr { 
                display: block; 

            }

            /* Hide table headers (but not display: none;, for accessibility) */
            #no-more-tables thead tr { 
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            #no-more-tables tr { border: 1px solid #ccc; }

            #no-more-tables td { 
                /* Behave  like a "row" */
                border: none;
                border-bottom: 1px solid #eee; 
                position: relative;
                padding-left: 50%; 
                white-space: normal;
                text-align:left;      
            }

            #no-more-tables td:before { 
                /* Now like a table header */
                position: absolute;
                /* Top/left values mimic padding */
                top: 6px;
                left: 6px;
                width: 45%; 
                padding-right: 10px; 
                white-space: nowrap;
                text-align:left;
                font-weight: bold;
            }

            /*
            Label the data
            */
            #no-more-tables td:before { content: attr(data-title); }
        }
    </style>
    <!--<![endif]-->
    <style type="text/css">
        .news_error{
            color:red;
        }
        .succMsg{
            background:#E2F3DA ;
            border: 1px solid #9ADF8F;
            color:#556652 !important;
            width:100% !important;
            padding:8px 8px 8px 36px;
            text-align:left;
            margin:5px;
            margin-left: 0px;
            margin-top: 30px;
            width:750px !important;
        }
        .errMsg{

            background:#FFCECE ;     
            border: 1px solid #DF8F8F;
            color:#665252 !important;
            width:100% !important;
            padding:8px 8px 8px 36px; 
            text-align:left;
            margin:5px;
            margin-left: 0px;
            margin-top: 30px;
            width:750px !important;

        }
        #gridTbl{width: 100%;}
        .table{width:100%;margin-bottom:18px;}.table th,.table td{padding:8px;line-height:18px;text-align:left;vertical-align:top;border-top:1px solid #E1E1E1}
        .table th{font-weight:bold;}
        .table thead th{vertical-align:bottom;}
        .table thead:first-child tr th,.table thead:first-child tr td{border-top:0;}
        .table tbody+tbody{border-top:2px solid #ddd;}
        .table-condensed th,.table-condensed td{padding:4px 5px;background-color: #ffffff;}
        .table-bordered{border:1px solid #ddd;border-collapse:separate;*border-collapse:collapsed;-webkit-border-radius:4px;-moz-border-radius:4px;border-radius:4px;}.table-bordered th+th,.table-bordered td+td,.table-bordered th+td,.table-bordered td+th{border-left:1px solid #E1E1E1;background-color: #ffffff;}
        .table-bordered thead:first-child tr:first-child th,.table-bordered tbody:first-child tr:first-child th,.table-bordered tbody:first-child tr:first-child td{border-top:0;}
        .table-bordered thead:first-child tr:first-child th:first-child,.table-bordered tbody:first-child tr:first-child td:first-child{-webkit-border-radius:4px 0 0 0;-moz-border-radius:4px 0 0 0;border-radius:4px 0 0 0;}
        .table-bordered thead:first-child tr:first-child th:last-child,.table-bordered tbody:first-child tr:first-child td:last-child{-webkit-border-radius:0 4px 0 0;-moz-border-radius:0 4px 0 0;border-radius:0 4px 0 0;}
        .table-bordered thead:last-child tr:last-child th:first-child,.table-bordered tbody:last-child tr:last-child td:first-child{-webkit-border-radius:0 0 0 4px;-moz-border-radius:0 0 0 4px;border-radius:0 0 0 4px;}
        .table-bordered thead:last-child tr:last-child th:last-child,.table-bordered tbody:last-child tr:last-child td:last-child{-webkit-border-radius:0 0 4px 0;-moz-border-radius:0 0 4px 0;border-radius:0 0 4px 0;}
        .table-striped tbody tr:nth-child(odd) td,.table-striped tbody tr:nth-child(odd) th{background-color:#f9f9f9;}
        .table tbody tr:hover td,.table tbody tr:hover th{background-color:#f5f5f5;}
        .alignCenter{text-align: center;}
        .image_error{color:red;}
        .succMsg{background:#E2F3DA ;border: 1px solid #9ADF8F;color:#556652 !important;padding:8px 8px 8px 36px;text-align:left;margin:5px;margin-left: 0px;margin-top: 30px;width:505px !important;}
        .errMsg{background:#FFCECE ;border: 1px solid #DF8F8F;color:#665252 !important;padding:8px 8px 8px 36px; text-align:left;margin:5px;margin-left: 0px;margin-top: 30px;width:505px !important;}
        .printCode{background: lightYellow none repeat scroll 0 0 !important;border: 1px inset orange !important;height: 36px !important;margin: 10px !important;overflow: auto !important;padding: 6px !important;text-align: left !important;color: black !important;width:auto !important;}
    </style>    

    <?php
        if(strtolower($action)==strtolower('gridview')){ 

            require_once("Pager.php");

        ?> 
        <div id="poststuff">
            <table><tr><td><a href="https://twitter.com/FreeAdsPost" class="twitter-follow-button" data-show-count="false" data-size="large" data-show-screen-name="false">Follow @FreeAdsPost</a>
                        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></td>
                    <td>
                        <a target="_blank" title="Donate" href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&amp;business=nvgandhi123@gmail.com&amp;item_name=Scroller News&amp;item_number=scroll news support&amp;no_shipping=0&amp;no_note=1&amp;tax=0&amp;currency_code=USD&amp;lc=US&amp;bn=PP%2dDonationsBF&amp;charset=UTF%2d8">
                            <img id="help us for free plugin" height="30" width="90" src="http://www.i13websolution.com/images/paypaldonate.jpg" border="0" alt="help us for free plugin" title="help us for free plugin">
                        </a>
                    </td>
                </tr>
            </table>
            <span><h3 style="color: blue;"><a target="_blank" href="http://www.i13websolution.com/wordpress-pro-plugins/wordpress-vertical-news-scroller-pro.html">UPGRADE TO PRO VERSION</a></h3></span>

            <?php 

                $messages=get_option('scrollnews_messages'); 
                $type='';
                $message='';
                if(isset($messages['type']) and $messages['type']!=""){

                    $type=$messages['type'];
                    $message=$messages['message'];

                }  


                if($type=='err'){ echo "<div class='errMsg'>"; echo $message; echo "</div>";}
                else if($type=='succ'){ echo "<div class='succMsg'>"; echo $message; echo "</div>";}


                update_option('scrollnews_messages', array());     
            ?>

            <div id="post-body" class="metabox-holder columns-2">  
                <div id="post-body-content" >
                    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
                    <h2>News <a class="button add-new-h2" href="admin.php?page=Scrollnews-settings&action=addedit">Add New</a> </h2>
                    <br/>    

                    <form method="POST" action="admin.php?page=Scrollnews-settings&action=deleteselected" id="posts-filter">


                        <div class="alignleft actions">
                            <select name="action_upper">
                                <option selected="selected" value="-1">Bulk Actions</option>
                                <option value="delete">delete</option>
                            </select>
                            <input type="submit" value="Apply" class="button-secondary action" id="deleteselected" name="deleteselected">
                        </div>
                        <br/>  
                        <br/>  
                        <br class="clear">
                        <div id="no-more-tables">
                            <table cellspacing="0" id="gridTbl" class="table-bordered table-striped table-condensed cf" >
                                <thead>
                                    <tr>
                                        <th class="manage-column column-cb check-column" scope="col"><input type="checkbox"></th>
                                        <th>Title</th>
                                        <th><span>Published On</span></th>
                                        <th><span>Edit</span></th>
                                        <th><span>Delete</span></th>
                                    </tr> 
                                </thead>

                                <tbody id="the-list">
                                    <?php
                                        $query="SELECT * FROM ".$wpdb->prefix."scroll_news order by createdon desc";
                                        $rows=$wpdb->get_results($query,'ARRAY_A');

                                        if(count($rows) > 0){

                                            $params = array(
                                                'mode'     => 'Sliding',
                                                'perPage'  => 10,
                                                'delta'    => 10,
                                                'itemData' => $rows,
                                                'fixFileName' => false,
                                            );
                                            // generate pager object
                                            @$pager =& Pager::factory($params);

                                            // get data for current page and print
                                            $pageset = $pager->getPageData();

                                            $rows = $pageset;


                                            foreach($rows as $row){ 

                                                $id=$row['id'];
                                                $editlink="admin.php?page=Scrollnews-settings&action=addedit&id=$id";
                                                $deletelink="admin.php?page=Scrollnews-settings&action=delete&id=$id";

                                            ?>
                                            <tr valign="top" >
                                                <td class="alignCenter check-column"   data-title="Select Record" ><input type="checkbox" value="<?php echo $row['id'] ?>" name="news[]"></td>
                                                <td class="alignCenter"   data-title="Name" ><strong><?php echo stripslashes($row['title']) ?></strong></td>  
                                                <td class="alignCenter"   data-title="Published On"><span><?php echo $row['createdon'] ?></span></td>
                                                <td class="alignCenter"   data-title="edit"><strong><a href='<?php echo $editlink; ?>' title="edit">Edit</a></strong></td>  
                                                <td class="alignCenter"   data-title="Delete"><strong><a href='<?php echo $deletelink; ?>' onclick="return confirmDelete();"  title="delete">Delete</a> </strong></td>  
                                            </tr>

                                            <?php 
                                            } 
                                        }
                                        else{
                                        ?>

                                        <tr valign="top" class="" id="">
                                            <td colspan="5" data-title="No Record" align="center"><strong>No News Found</strong></td>  
                                        </tr>
                                        <?php 
                                        } 
                                    ?>      
                                </tbody>
                            </table>
                        </div>
                        <?php
                            if(sizeof($rows)>0){

                                $links = $pager->getLinks();
                                echo "<div class='paggingDiv' style='padding-top:10px'>";
                                echo $links['all'];
                                echo "</div>";
                            }
                        ?>
                        <br/>
                        <div class="alignleft actions">
                            <select name="action">
                                <option selected="selected" value="-1">Bulk Actions</option>
                                <option value="delete">delete</option>
                            </select>
                            <input type="submit" value="Apply" class="button-secondary action" id="deleteselected" name="deleteselected">
                        </div>

                    </form>
                    <script type="text/JavaScript">

                        function  confirmDelete(){
                            var agree=confirm("Are you sure you want to delete this news ?");
                            if (agree)
                                return true ;
                            else
                                return false;
                        }
                    </script>


                    <br class="clear">
                </div>
                <div id="postbox-container-1" class="postbox-container"> 
                    <div class="postbox"> 
                        <h3 class="hndle"><span></span>Best WP Hosting</h3> 
                        <div class="inside">
                            <center><a target="_blank" href="http://www.shareasale.com/r.cfm?b=531904&u=675922&m=41388&urllink=&afftrack="><img src="http://www.shareasale.com/image/41388/sas_banner_250x250.jpg" alt="WP Engine" border="0"></a></center>

                            <div style="margin:10px 5px">

                            </div>
                        </div></div>

                    <div class="postbox"> 
                        <h3 class="hndle"><span></span>Access All Themes One price</h3> 
                        <div class="inside">
                            <center><a href="http://www.elegantthemes.com/affiliates/idevaffiliate.php?id=11715_0_1_10" target="_blank"><img border="0" src="http://www.elegantthemes.com/affiliates/banners/300x250.gif" width="250" height="250"></a></center>

                            <div style="margin:10px 5px">

                            </div>
                        </div></div>


                </div>

            </div>  
        </div>  

        <?php 
        }   
        else if(strtolower($action)==strtolower('addedit')){
        ?>
        <br/>

        <span><h3 style="color: blue;"><a target="_blank" href="http://www.i13websolution.com/wordpress-pro-plugins/wordpress-vertical-news-scroller-pro.html">UPGRADE TO PRO VERSION</a></h3></span>
        <?php        
            if(isset($_POST['btnsave'])){

                //edit save
                if(isset($_POST['newsid'])){

                    //add new

                    $title=trim(addslashes($_POST['newstitle']));
                    $newsurl=trim($_POST['newsurl']);
                    $contant=trim(addslashes($_POST['newscont']));
                    $newsId=trim($_POST['newsid']);

                    $location='admin.php?page=Scrollnews-settings';

                    try{
                        $query = "update ".$wpdb->prefix."scroll_news set title='$title',content='$contant',
                        custom_link='$newsurl' where id=$newsId";
                        $wpdb->query($query); 

                        $scrollnews_messages=array();
                        $scrollnews_messages['type']='succ';
                        $scrollnews_messages['message']='News updated successfully.';
                        update_option('scrollnews_messages', $scrollnews_messages);


                    }
                    catch(Exception $e){

                        $scrollnews_messages=array();
                        $scrollnews_messages['type']='err';
                        $scrollnews_messages['message']='Error while updating news.';
                        update_option('scrollnews_messages', $scrollnews_messages);
                    }  

                    echo "<script> location.href='$location';</script>";
                }
                else{

                    //add new

                    $title=trim(addslashes($_POST['newstitle']));
                    $newsurl=trim($_POST['newsurl']);
                    $contant=trim(addslashes($_POST['newscont']));
                    $createdOn=date( 'Y-m-d H:i:s', current_time( 'mysql' ));
                    if(get_option('time_format')=='H:i')
                        $createdOn=date('Y-m-d H:i:s',strtotime(current_time('mysql')));
                    else   
                        $createdOn=date('Y-m-d h:i:s',strtotime(current_time('mysql')));


                    $location='admin.php?page=Scrollnews-settings';

                    try{
                        $query = "INSERT INTO ".$wpdb->prefix."scroll_news (title, content, createdon,custom_link) 
                        VALUES ('$title','$contant','$createdOn','$newsurl')";
                        $wpdb->query($query); 

                        $scrollnews_messages=array();
                        $scrollnews_messages['type']='succ';
                        $scrollnews_messages['message']='New news added successfully.';
                        update_option('scrollnews_messages', $scrollnews_messages);


                    }
                    catch(Exception $e){

                        $scrollnews_messages=array();
                        $scrollnews_messages['type']='err';
                        $scrollnews_messages['message']='Error while adding news.';
                        update_option('scrollnews_messages', $scrollnews_messages);
                    }  

                    echo "<script> location.href='$location';</script>";          

                } 

            }
            else{ 

            ?>
            <table><tr><td><a href="https://twitter.com/FreeAdsPost" class="twitter-follow-button" data-show-count="false" data-size="large" data-show-screen-name="false">Follow @FreeAdsPost</a>
                        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></td>
                    <td>
                        <a target="_blank" title="Donate" href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&amp;business=nvgandhi123@gmail.com&amp;item_name=Scroller News&amp;item_number=scroll news support&amp;no_shipping=0&amp;no_note=1&amp;tax=0&amp;currency_code=USD&amp;lc=US&amp;bn=PP%2dDonationsBF&amp;charset=UTF%2d8">
                            <img id="help us for free plugin"  height="30" width="90" src="http://www.i13websolution.com/images/paypaldonate.jpg" border="0" alt="help us for free plugin" title="help us for free plugin">
                        </a>

                    </td>
                </tr></table>
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <div class="wrap">
                            <?php if(isset($_GET['id']) and $_GET['id']>0)
                                { 

                                    $id= $_GET['id'];
                                    $query="SELECT * FROM ".$wpdb->prefix."scroll_news WHERE id=$id";
                                    $myrow  = $wpdb->get_row($query);

                                    if(is_object($myrow)){

                                        $title=stripslashes($myrow->title);
                                        $newsurl=$myrow->custom_link;
                                        $contant=stripslashes($myrow->content);

                                    }   

                                ?>

                                <h2>Update News </h2>

                                <?php }else{ 

                                    $title='';
                                    $newsurl='';
                                    $contant='';

                                ?>
                                <h2>Add News </h2>
                                <?php } ?>

                            <div id="poststuff">
                                <div id="post-body" class="metabox-holder columns-2">
                                    <div id="post-body-content">
                                        <form method="post" action="" id="addnews" name="addnews">

                                            <div class="stuffbox" id="namediv" style="width:100%">
                                                <h3><label for="link_name">News Title</label></h3>
                                                <div class="inside">
                                                    <input type="text" id="newstitle"  class="required"  size="30" name="newstitle" value="<?php echo $title;?>">
                                                    <div style="clear:both"></div>
                                                    <div></div>
                                                    <div style="clear:both"></div>
                                                    <p><?php _e('This title will scroll'); ?></p>
                                                </div>
                                            </div>
                                            <div class="stuffbox" id="namediv" style="width:100%">
                                                <h3><label for="link_name">News Url</label></h3>
                                                <div class="inside">
                                                    <input type="text" id="newsurl" class="required url"   size="30" name="newsurl" value="<?php echo $newsurl; ?>">
                                                    <div style="clear:both"></div>
                                                    <div></div>
                                                    <div style="clear:both"></div>
                                                    <p><?php _e('On news title click users will redirect to this url.'); ?></p>
                                                </div>
                                            </div>
                                            <div class="stuffbox" id="namediv" style="width:100%">
                                                <h3><label for="link_name">News Content</label></h3>
                                                <div class="inside">
                                                    <textarea cols="90" class="required" style="width:100%" rows="6" id="newscont" name="newscont"><?php echo $contant; ?></textarea>
                                                    <div style="clear:both"></div>
                                                    <div></div>
                                                    <div style="clear:both"></div>
                                                    <p><?php _e('Two three lines summary'); ?></p>
                                                </div>
                                            </div>
                                            <?php if(isset($_GET['id']) and $_GET['id']>0){ ?> 
                                                <input type="hidden" name="newsid" id="newsid" value="<?php echo $_GET['id'];?>">
                                                <?php
                                                } 
                                            ?>
                                            <input type="submit" name="btnsave" id="btnsave" value="Save Changes" class="button-primary">&nbsp;&nbsp;<input type="button" name="cancle" id="cancle" value="Cancel" class="button-primary" onclick="location.href='admin.php?page=Scrollnews-settings'">

                                        </form> 
                                        <script>
                                            var $n = jQuery.noConflict();  
                                            $n(document).ready(function() {  
                                                    $n("#addnews").validate({
                                                            errorClass: "news_error",
                                                            errorPlacement: function(error, element) {
                                                                error.appendTo( element.next().next().next());
                                                            }

                                                    })
                                            });

                                        </script> 

                                    </div>
                                </div>
                            </div>  
                        </div>      
                    </div>
                    <div id="postbox-container-1" class="postbox-container"> 

                        <div class="postbox"> 
                            <h3 class="hndle"><span></span>Access All Themes One price</h3> 
                            <div class="inside">
                                <center><a href="http://www.elegantthemes.com/affiliates/idevaffiliate.php?id=11715_0_1_10" target="_blank"><img border="0" src="http://www.elegantthemes.com/affiliates/banners/300x250.gif" width="250" height="250"></a></center>

                                <div style="margin:10px 5px">

                                </div>
                            </div></div>

                        <div class="postbox"> 
                            <h3 class="hndle"><span></span>Find Low Competition Keywords</h3> 
                            <div class="inside">
                                <center><a href="http://42eb4jw9flkrluf45q50poct4o.hop.clickbank.net/?tid=FP76479Y" target="_top"><img src="http://nichefinder.bradcallen.com/affiliates/banners/320x250.jpg" width="250" height="250" border="1" ALT="Click to Visit"></a></center>

                                <div style="margin:10px 5px">

                                </div>
                            </div></div>


                    </div> 

                </div>         

            </div>
            <?php 
            } 
        }else if(strtolower($action)==strtolower('delete')){

            $location='admin.php?page=Scrollnews-settings';
            $deleteId=(int)$_GET['id'];

            try{
                $query = "delete from  ".$wpdb->prefix."scroll_news where id=$deleteId";
                $wpdb->query($query); 

                $scrollnews_messages=array();
                $scrollnews_messages['type']='succ';
                $scrollnews_messages['message']='News deleted successfully.';
                update_option('scrollnews_messages', $scrollnews_messages);


            }
            catch(Exception $e){

                $scrollnews_messages=array();
                $scrollnews_messages['type']='err';
                $scrollnews_messages['message']='Error while deleting news.';
                update_option('scrollnews_messages', $scrollnews_messages);
            }  

            echo "<script> location.href='$location';</script>";

        }  
        else if(strtolower($action)==strtolower('deleteselected')){

            $location='admin.php?page=Scrollnews-settings'; 
            if(isset($_POST) and isset($_POST['deleteselected']) and  ( $_POST['action']=='delete' or $_POST['action_upper']=='delete')){

                if(sizeof($_POST['news']) >0){

                    $deleteto=$_POST['news'];
                    $implode=implode(',',$deleteto);   

                    try{
                        $query = "delete from  ".$wpdb->prefix."scroll_news where id in ($implode)";
                        $wpdb->query($query); 

                        $scrollnews_messages=array();
                        $scrollnews_messages['type']='succ';
                        $scrollnews_messages['message']='selected news deleted successfully.';
                        update_option('scrollnews_messages', $scrollnews_messages);


                    }
                    catch(Exception $e){

                        $scrollnews_messages=array();
                        $scrollnews_messages['type']='err';
                        $scrollnews_messages['message']='Error while deleting news.';
                        update_option('scrollnews_messages', $scrollnews_messages);
                    }  

                    echo "<script> location.href='$location';</script>";


                }
                else{

                    echo "<script> location.href='$location';</script>";   
                }

            }
            else{

                echo "<script> location.href='$location';</script>";      
            }

        }    
    }  

    class verticalScroll extends WP_Widget {

        function verticalScroll() {

            $widget_ops = array('classname' => 'verticalScroll', 'description' => 'Vertical news scroll');
            $this->WP_Widget('verticalScroll', 'Vertical news scroll',$widget_ops);
        }

        function widget( $args, $instance ) {

            if(is_array($args)){
                extract( $args );
            }

            $title = apply_filters('widget_title', empty( $instance['title'] ) ? 'News Scroll' :$instance['title']);   
            include_once(ABSPATH . WPINC . '/feed.php');
            echo @$before_widget;
            echo @$before_title.$title.$after_title;   
            $maxitem=(int)empty( $instance['maxitem'] ) ? 5 :$instance['maxitem']; 
            $padding=(int)empty( $instance['padding'] ) ? 5 :$instance['padding']; 
            $add_link_to_title=($instance['add_link_to_title']==null) ? 0 :$instance['add_link_to_title']; 
            $show_content=($instance['show_content']==null) ? 0 :$instance['show_content']; 
            $delay=(int)empty( $instance['delay'] ) ? 5 :$instance['delay'];    
            $height=(int)empty( $instance['height'] ) ? 200 :$instance['height']; 
            $scrollamt=(int)empty( $instance['scrollamount'] ) ? 1 :$instance['scrollamount']; 
            global $wpdb;
            $query="SELECT * FROM ".$wpdb->prefix."scroll_news order by createdon desc limit $maxitem";
            $rows=$wpdb->get_results($query,'ARRAY_A');
        ?>
        <link rel="stylesheet" type="text/css" media="all" href="<?php echo plugins_url('vertical-news-scroller/css/newsscrollcss.css'); ?>" />
        <marquee height='<?php echo $height; ?>'  onmouseout="this.start()" onmouseover="this.stop()" scrolldelay="<?php echo $delay; ?>" scrollamount="<?php echo $scrollamt; ?>" direction="up" behavior="scroll" >
            <?php

                foreach($rows as $row){
                ?>
                <div style="padding:<?php echo $padding; ?>px">
                    <div class="newsscroller_title"><?php if($add_link_to_title){?><a href='<?php echo $row['custom_link']; ?>' target="_blank"><?php } ?><?php echo  stripslashes($row['title']) ; ?><?php if($add_link_to_title){?></a><?php } ?></div>
                    <div style="clear:both"></div>
                    <?php if($show_content){ ?>
                        <div class="scrollercontent">
                            <?php echo stripslashes(nl2br($row['content'])); ?>
                        </div>
                        <?php } ?>       
                </div>

                <?php 
                }

            ?>
        </marquee>

        <?php
            echo $after_widget; 

        }



        function update( $new_instance, $old_instance ) {

            $instance = $old_instance;
            $instance['title'] = strip_tags($new_instance['title']);
            $instance['add_link_to_title'] = $new_instance['add_link_to_title'];
            $instance['maxitem'] = strip_tags($new_instance['maxitem']);
            $instance['padding'] = $new_instance['padding'];
            $instance['show_content'] = $new_instance['show_content'];
            $instance['delay'] = $new_instance['delay'];
            $instance['scrollamount'] = $new_instance['scrollamount'];
            $instance['height'] = $new_instance['height'];
            return $instance;


        }
        function form( $instance ) {

            //Defaults
            $instance = wp_parse_args( (array) $instance, array('title' => 'News','maxitem' => 5,'padding' => 5,'show_content' => 1,'delay'=>5,'scrollamount'=>1,'add_link_to_title'=>1,'height'=>200));
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
        </p>
        <p>
            <input id="<?php echo $this->get_field_id('add_link_to_title'); ?>" name="<?php echo $this->get_field_name('add_link_to_title'); ?>"
                type="checkbox" <?php checked($instance['add_link_to_title'], 1); ?> value="1" />
            <label for="<?php echo $this->get_field_id('add_link_to_title'); ?>">Add link to news title</label>
        </p>
        <p><label for="<?php echo $this->get_field_id('maxitem'); ?>">Max item from news:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('maxitem'); ?>" name="<?php echo $this->get_field_name('maxitem'); ?>"
                type="text" value="<?php echo $instance['maxitem']; ?>" />
        </p>

        <p><label for="<?php echo $this->get_field_id('height'); ?>">Height of scroller:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo $instance['height']; ?>" />px
        </p>

        <p><label for="<?php echo $this->get_field_id('padding'); ?>">Padding:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('padding'); ?>" name="<?php echo $this->get_field_name('padding'); ?>" type="text" value="<?php echo $instance['padding']; ?>" />px
        </p>

        <p>
            <input id="<?php echo $this->get_field_id('show_content'); ?>" name="<?php echo $this->get_field_name('show_content'); ?>"
                type="checkbox" <?php checked($instance['show_content'], 1); ?> value="1" />
            <label for="<?php echo $this->get_field_id('show_content'); ?>">Show news content</label>
        </p>

        <p><label for="<?php echo $this->get_field_id('delay'); ?>">Delay :</label>
            <input class="widefat" id="<?php echo $this->get_field_id('delay'); ?>" name="<?php echo $this->get_field_name('delay'); ?>" type="text" value="<?php echo $instance['delay']; ?>" />Micro Sec
        </p>
        <p>

        <p><label for="<?php echo $this->get_field_id('scrollamount'); ?>">Scroll amount :</label>
            <input class="widefat" id="<?php echo $this->get_field_id('scrollamount'); ?>" name="<?php echo $this->get_field_name('scrollamount'); ?>" type="text" value="<?php echo $instance['scrollamount']; ?>" />(Ie 1,2,3)
        </p>
        <p>

        <?php
        } // function form
    } // widget class

?>