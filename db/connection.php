<?php
class ashtel
{


	// var $con;//global variable for connection;
    // var $host="localhost";//host Address;
    // var $user="heatz_usr";//database User;
    // var $pswd="heatz654123!";//database Password;
    // var $db="heatz_db_2k20";//database name;
    // var $base_url = "https://www.heatz.store";


	var $con;//global variable for connection;
    var $host="localhost";//host Address;
    var $user="root";//database User;
    var $pswd="";//database Password;
    var $db="heatz";//database name;
    var $base_url = "http://localhost/heatz/cloud/";


	/*..............................................*/

 function __construct() {

        session_start();
        date_default_timezone_set("Asia/Dubai");

        $current_page = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if(!isset($_SESSION['cloud_id']) && $current_page != $this->base_url){
            header("location:".$this->base_url);
        }


        $this->con = mysqli_connect($this->host, $this->user, $this->pswd, $this->db) or die("connection error");
    }
    function rename_image($file_name) {
        $temp = explode(".", $file_name);
        $todays_date = date("mdYHis");
        $file_name = $temp[0] . "-" . $todays_date . "." . $temp[1];
        return $file_name;
    }
    function select_all($table) {
        $result = $this->con->query("select * from $table") or die(mysqli_error($this->con));
        return $result;
    }
     function select_max_sort_order_downloads() {
        $result = $this->con->query("select max(sort_order) as sort_order from download_links");
        $row = mysqli_fetch_assoc($result);
        return $row['sort_order'];
    }
    function select_all_active_by_order($table, $where = "") {
        $result = $this->con->query("select * from $table where status=1  $where order by sort_order ") or die(mysqli_error($this->con));
        return $result;
    }
    function select_features_division($table) {
        $result = $this->con->query("select * from $table where status=1  and featured=1 order by sort_order ") or die(mysqli_error($this->con));
        return $result;
    }
    function select_all_active_division_by_order($where = "") {
        $result = $this->con->query("select main_division.* from main_division join division ON main_division.id=division.main_id where main_division.status=1 $where GROUP BY main_division.id order by main_division.sort_order ") or die(mysqli_error($this->con));
        return $result;
    }
    function select_all_featured_products($table, $id) {
        $result = $this->con->query("select * from $table where main_division_id=$id and status=1 and featured=1 order by sort_order") or die(mysqli_error($this->con));
        return $result;
    }
    function select_all_active_by_id($table, $id) {
        if ($table == "division") {
            $result = $this->con->query("select * from $table where main_id=$id and status=1") or die(mysqli_error($this->con));
        } else if ($table == "products") {
            $result = $this->con->query("select * from $table where division_id=$id and status=1") or die(mysqli_error($this->con));
        } else if ($table == "model_images" ) {
            $result = $this->con->query("select * from $table where product_id=$id AND isCover=0") or die(mysqli_error($this->con));
        } else if ($table == "spec") {
            $result = $this->con->query("select * from $table where product_id=$id") or die(mysqli_error($this->con));
        } else {
            $result = $this->con->query("select * from $table where id=$id") or die(mysqli_error($this->con));
        }
        return $result;
    }


	function select_product_by_search($search="", $offset=NULL,$limit=0) {
        $where='1=1';
        if($search !=""){
            $where.=" AND (main_division.name LIKE '%$search%' or  products.name LIKE '%$search%' or  category.name LIKE '%$search%' or  products.title LIKE '%$search%' or  products.sub_title LIKE '%$search%' or  products.features LIKE '%$search%')";
		}
		$limit_ = "";
		if($limit>0){
			 $limit_ = " LIMIT $offset,$limit";
		}

        $result = $this->con->query("select main_division.name as main_div, products.* from main_division  join products on main_division.id= products.main_division_id join category on category.id= main_division.category  where $where AND products.status=1 group by products.id order by products.sort_order  $limit_");
        return $result;
    }
    function select_details($id) {
        $result = $this->con->query("select spec.*,products.name as name, products.spec_align as spec_align, products.features as features from spec left join products on spec.product_id=products.id where spec.product_id=$id order by sort_order") or die(mysqli_error($this->con));
        return $result;
    }
    function select_all_active($table) {
        $result = $this->con->query("select * from $table where status=1 ORDER BY sort_order") or die(mysqli_error($this->con));
        return $result;
    }
    function select_menu_preload_images() {
        $result = $this->con->query("select * from division where status=1 order by sort_order ") or die(mysqli_error($this->con));
        $arr = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $arr[] = "'" . $row['hover_img'] . "'";
        }
        return implode(",", $arr);
    }
    function select_all_by_sort_order($table, $where = " WHERE 1=1 ") {
        $result = $this->con->query("select * from $table $where order by sort_order") or die(mysqli_error($this->con));
        return $result;
    }
    function select_all_type_by_sort_order($table, $where = " WHERE 1=1 ") {
        $result = $this->con->query("select * from $table $where order by main_id, sort_order") or die(mysqli_error($this->con));
        return $result;
    }
    function select_all_featured_by_id($id) {
        $result = $this->con->query("select featured_details.*,products.name as model,products.title as title from featured_details left join products on products.id = featured_details.product_id where feature_id='$id'") or die(mysqli_error($this->con));
        return $result;
    }

    function select_by_id($table, $id) {
        $result = $this->con->query("select * from $table where id =$id") or die(mysqli_error($this->con));
        return mysqli_fetch_assoc($result);
    }


	function select_by_field($table, $field, $id) {
        $result = $this->con->query("select * from $table where $field ='$id'") or die(mysqli_error($this->con));
        return mysqli_fetch_assoc($result);
    }

    function select_by_category($table, $field, $id) {
        $result = $this->con->query("select * from $table where $field ='$id'") or die(mysqli_error($this->con));
        return $result;
    }

    function select_by_category_name($name) {
         $result = $this->con->query("select main_division.* from category left join main_division on category.id=main_division.category  where category.name ='$name' AND main_division.status=1 ORDER BY main_division.sort_order asc") or die(mysqli_error($this->con));
        return $result;
    }

    function select_by_id_field($table,  $id) {
        $result = $this->con->query("select * from $table where product_id ='$id'") or die(mysqli_error($this->con));
        return $result;
    }

    function select_by_id_featured($id) {
        $result = $this->con->query("select * from featured_details where feature_id ='$id'") or die(mysqli_error($this->con));
        return $result;
    }

	function select_by_id_featured1($id) {
        $result = $this->con->query("select products.name as name from featured_details left join products on products.id=featured_details.product_id where featured_details.feature_id ='$id'") or die(mysqli_error($this->con));
        return $result;
    }

    function select_products_by_division($table, $id) {
        $result = $this->con->query("select * from $table where main_division_id =$id and status=1 order by sort_order") or die(mysqli_error($this->con));
        return $result;
    }

    function select_by_result($table, $id) {
        $result = $this->con->query("select * from $table where id =$id and status=1 order by sort_order") or die(mysqli_error($this->con));
        return $result;
    }
    function delete_by_id($table, $id) {
        $result = $this->con->query("delete from $table where id =$id");
        return $result;
    }

	function delete_by_where($table, $where="1!=1") {
        $result = $this->con->query("delete from $table  where $where") or die(mysqli_error($this->con));
       	return $result;
    }
    function select_id_by_name($table, $name) {
        $result = $this->con->query("select id from $table where name ='$name'") or die(mysqli_error($this->con));
        $row = mysqli_fetch_assoc($result);
        return $row['id'];
    }
    function select_name_by_id($table, $id) {
        $result = $this->con->query("select name from $table where id ='$id'") or die(mysqli_error($this->con));
        $row = mysqli_fetch_assoc($result);
        return $row['name'];
    }
    function select_image_by_id($table, $id) {
        $result = $this->con->query("select image from $table where id ='$id'") or die(mysqli_error($this->con));
        $row = mysqli_fetch_assoc($result);
        return $row['image'];
    }
    function select_by_condition($table, $where) {
        $result = mysqli_query($this->con, "select * from $table where " . $where);
        return $result;
    }
    function change_status($table, $status, $id) {
        $this->con->query("update  $table set status = '$status' where id=$id ") or die(mysqli_error($this->con));
    }
    function select_product_by_division($division_id) {
        $result = $this->con->query("select * from products where division_id=$division_id");
        return $result;
    }
    function select_product_by_main($id) {
        $result = $this->con->query("select main_division.name as main_div, products.* from main_division left join products on main_division.id= products.main_division_id where main_division.category='$id' and products.status=1 order by products.sort_order");
        return $result;
    }
    function select_product_by_main_division($id, $offset=NULL,$limit=NULL,$division="ALL") {
        $where='';
        if($division !="ALL")
            $where =" AND main_division.name= '$division'";
        $result = $this->con->query("select main_division.name as main_div, products.* from main_division left join products on main_division.id= products.main_division_id where main_division.category='$id' and products.status=1 $where order by products.sort_order  LIMIT $offset,$limit");
        return $result;
    }
    function select_product_by_main_count($id,$division="ALL") {
        $where='';
        if($division !="ALL")
            $where =" AND main_division.name= '$division'";
        $result = $this->con->query("select count(*) as count from main_division left join products on main_division.id= products.main_division_id where main_division.category='$id' and products.status=1 $where order by products.sort_order");
        $row = mysqli_fetch_assoc($result);
        return $row['count'];
    }
    function select_prodcuts_by_id($id) {
        $result = $this->con->query("select * from products where id=$id and status=1 order by sort_order");
        return $result;
    }
    function select_product_by_name_($name) {
		$name = mysqli_real_escape_string($this->con, $name);
        $result = $this->con->query("select * from products where name='$name' and status=1 order by sort_order");
        return $result;
    }
    function select_active_product_by_division($division_id) {
        $result = $this->con->query("select * from products where division_id=$division_id && status=1 order by sort_order");
        return $result;
    }
    function select_product_by_name($name) {

		$name = mysqli_real_escape_string($this->con, $name);
        $result = $this->con->query("select products.*, division.name as division_name, main_division.name as main_division_name  from products
		left join division ON  division.id = products.division_id left join main_division ON  main_division.id = products.main_division_id
		where products.name='$name'") or die(mysqli_error($this->con));
        return mysqli_fetch_assoc($result);
    }
    function select_fetaures_by_product($product_id) {
        $result = $this->con->query("select * from features where product_id=$product_id order by sort_order");
        return $result;
    }
    function select_active_fetaures_by_product($product_id) {
        $result = $this->con->query("select * from features where product_id=$product_id && status=1 order by sort_order");
        return $result;
    }
    function select_highlites_fetaure_by_highlights($highlights_id) {
        $result = $this->con->query("select * from highlights_features where highlights_id=$highlights_id order by sort_order");
        return $result;
    }
    function select_active_highlites_fetaure_by_highlights($highlights_id) {
        $result = $this->con->query("select * from highlights_features where highlights_id=$highlights_id && status=1 order by sort_order");
        return $result;
    }
    function select_price_by_product($product_id) {
        $result = $this->con->query("select * from price where product_id=$product_id order by sort_order");
        return $result;
    }
    function select_spec_by_product($product_id) {
        $result = $this->con->query("select * from spec where product_id=$product_id order by sort_order");
        return $result;
    }
    function select_name($table) {
        $result = $this->con->query("select name from $table where product_id=$product_id order by sort_order");
        return $result;
    }
    function select_active_spec_by_product($product_id) {
        $result = $this->con->query("select * from spec where product_id=$product_id && status=1 order by sort_order") or die(mysqli_error($this->con));
        return $result;
    }
    function select_box_by_product($product_id) {
        $result = $this->con->query("select * from box_image where product_id=$product_id order by sort_order");
        return $result;
    }
    function select_active_box_by_product($product_id) {
        $result = $this->con->query("select * from box_image where product_id=$product_id && status=1 order by sort_order") or die(mysqli_error($this->con));
        return $result;
    }
    function select_division_bacground_by_product($product_id) {
        $result = $this->con->query("select division.backround from products
		left join division ON division.id = products.division_id
		where products.id=$product_id") or die(mysqli_error($this->con));
        $row = mysqli_fetch_assoc($result);
        return $row['backround'];
    }
    // banner
    function add_banner($file_name, $alt) {
        $this->con->query("insert into banner (name) values ('$file_name')") or die(mysqli_error($this->con));
    }
    function update_banner($alt, $id) {
        $this->con->query("update  banner set alt = '$alt' where id=$id ") or die(mysqli_error($this->con));
    }
    // Division
    function select_max_sort_order_division() {
        $result = $this->con->query("select max(sort_order) as sort_order from division  ");
        $row = mysqli_fetch_assoc($result);
        return $row['sort_order'];
    }
    function add_division($name, $caption_head, $sort_order) {
        $result = $this->con->query("insert into division (name, caption_head,sort_order) values ('$name', '$caption_head', $sort_order)") or die(mysqli_error($this->con));
        if ($result) $this->update_division_sort_order($sort_order, mysqli_insert_id($this->con));
    }
    function update_division($name, $caption_head, $sort_order, $id) {
        $result = $this->con->query("update  division set name = '$name', caption_head = '$caption_head', sort_order = $sort_order where id=$id ") or die(mysqli_error($this->con));
        if ($result) $this->update_division_sort_order($sort_order, $id);
    }
    function update_division_sort_order($sort_order, $id) {
        $result = $this->con->query("select * from division where  sort_order = $sort_order ") or die(mysqli_error($this->con));
        if (mysqli_num_rows($result) > 1) { // check if dufplicate
            $this->con->query("UPDATE division SET sort_order = $sort_order +1 WHERE id != $id ") or die(mysqli_error($this->con));
        }
    }
    // products
    function select_max_sort_order_product($division_id) {
        $result = $this->con->query("select max(sort_order) as sort_order from product where division_id = $division_id ");
        $row = mysqli_fetch_assoc($result);
        return $row['sort_order'];
    }

	 function select_all_products() {
        $result = $this->con->query("select products.*, division.name as division_name, main_division.name as main_division_name from products
		left join division ON division.id = products.division_id  left join main_division ON main_division.id = products.main_division_id
		order by products.division_id, products.name") or die(mysqli_error($this->con));
        return $result;
    }
    function add_product($name, $division_id, $menu_caption, $colours, $head_imge, $sort_order, $division_head) {
        $result = $this->con->query("insert into products (name, division_id, title,  colours, head_imge, sort_order, division_head) values ('$name', $division_id, '$menu_caption', '$colours', '$head_imge', $sort_order, $division_head)") or die(mysqli_error($this->con));
        if ($result) {
            $this->update_product_sort_order($division_id, $sort_order, mysqli_insert_id($this->con));
            $this->update_product_division_head($division_id, $division_head, mysqli_insert_id($this->con));
        }
    }
    function update_product($name, $division_id, $menu_caption, $colours, $head_imge, $sort_order, $division_head, $id) {
        $result = $this->con->query("update  products set name = '$name', division_id = '$division_id',title = '$menu_caption', colours = '$colours',head_imge = '$head_imge',  sort_order =$sort_order, division_head=$division_head where id=$id ") or die(mysqli_error($this->con));
        if ($result) {
            $this->update_product_sort_order($division_id, $sort_order, $id);
            $this->update_product_division_head($division_id, $division_head, $id);
        }
    }
    function update_product_sort_order($division_id, $sort_order, $id) {
        $result = $this->con->query("select * from products where  division_id=$division_id && sort_order = $sort_order ") or die(mysqli_error($this->con));
        if (mysqli_num_rows($result) > 1) { // check if dufplicate
            $this->con->query("UPDATE products SET sort_order = $sort_order +1 WHERE id != '$id' && sort_order >= $sort_order && division_id=$division_id") or die(mysqli_error($this->con));
        }
    }
    function update_product_division_head($division_id, $division_head, $id) {
        if ($division_head == 1) {
            $this->con->query("UPDATE products SET division_head = 0  WHERE id != $id &&  division_id=$division_id") or die(mysqli_error($this->con));
        }
    }
    function select_division_head_product_name($division_id) {
        $result = $this->con->query("select name from products where division_id=$division_id && division_head = 1");
        $row = mysqli_fetch_assoc($result);
        return $row['name'];
    }
    function select_division_head_product($division_id) {
        $result = $this->con->query("select name from products where division_id=$division_id && division_head = 1");
        $row = mysqli_fetch_assoc($result);
        return $row;
    }
    // features
    function select_max_sort_order_features($product_id) {
        $result = $this->con->query("select max(sort_order) as sort_order from features where product_id = $product_id ");
        $row = mysqli_fetch_assoc($result);
        return $row['sort_order'];
    }

	function select_max_sort_order($table, $product_id){

		$result = $this->con->query("select max(sort_order) as sort_order from $table where product_id = $product_id ");
		$row = mysqli_fetch_assoc($result);
		return $row['sort_order'];

	}

    function add_features($product_id, $heading, $details, $product_img, $sort_order) {
        $heading = mysqli_real_escape_string($this->con, $heading);
        $details = mysqli_real_escape_string($this->con, $details);
        $result = $this->con->query("insert into features (product_id, heading, details, product_img, sort_order) values ($product_id, '$heading', '$details', '$product_img', $sort_order)") or die(mysqli_error($this->con));
        if ($result) $this->update_features_sort_order($product_id, $sort_order, mysqli_insert_id($this->con));
    }
    function update_features($product_id, $heading, $details, $product_img, $sort_order, $id) {
        $heading = mysqli_real_escape_string($this->con, $heading);
        $details = mysqli_real_escape_string($this->con, $details);
        $result = $this->con->query("update features set product_id = $product_id, heading = '$heading', details = '$details', product_img='$product_img', sort_order = $sort_order where id=$id") or die(mysqli_error($this->con));
        if ($result) $this->update_features_sort_order($product_id, $sort_order, $id);
    }
    function update_features_sort_order($product_id, $sort_order, $id) {
        $result = $this->con->query("select * from features where  product_id=$product_id && sort_order = $sort_order ") or die(mysqli_error($this->con));
        if (mysqli_num_rows($result) > 1) { // check if dufplicate
            $this->con->query("UPDATE features SET sort_order = $sort_order +1 WHERE id != '$id' && sort_order >= $sort_order && product_id=$product_id") or die(mysqli_error($this->con));
        }
    }
    function select_max_sort_order_icons($product_id) {
        $result = $this->con->query("select max(sort_order) as sort_order from icons where product_id = $product_id ");
        $row = mysqli_fetch_assoc($result);
        return $row['sort_order'];
    }
    // specs
    function select_max_sort_order_specs($product_id) {
        $result = $this->con->query("select max(sort_order) as sort_order from spec where product_id = $product_id ");
        $row = mysqli_fetch_assoc($result);
        return $row['sort_order'];
    }
    function select_max_sort_order_price($product_id) {
        $result = $this->con->query("select max(sort_order) as sort_order from price where product_id = $product_id ");
        $row = mysqli_fetch_assoc($result);
        return $row['sort_order'];
    }
    function add_specs($product_id, $heading, $details, $sort_order) {
        $heading = mysqli_real_escape_string($this->con, $heading);
        $details = mysqli_real_escape_string($this->con, $details);
        $result = $this->con->query("insert into spec (product_id, heading, details, sort_order) values ($product_id, '$heading', '$details',  $sort_order)") or die(mysqli_error($this->con));
        if ($result) $this->update_specs_sort_order($product_id, $sort_order, mysqli_insert_id($this->con));
    }
    function update_specs($product_id, $heading, $details, $sort_order, $id) {
        $heading = mysqli_real_escape_string($this->con, $heading);
        $details = mysqli_real_escape_string($this->con, $details);
        $result = $this->con->query("update spec set product_id = $product_id, heading = '$heading', details = '$details', sort_order = $sort_order where id=$id") or die(mysqli_error($this->con));
        if ($result) $this->update_specs_sort_order($product_id, $sort_order, $id);
    }
    function update_specs_sort_order($product_id, $sort_order, $id) {
        $result = $this->con->query("select * from spec where  product_id=$product_id && sort_order = $sort_order ") or die(mysqli_error($this->con));
        if (mysqli_num_rows($result) > 1) { // check if dufplicate
            $this->con->query("UPDATE spec SET sort_order = $sort_order +1 WHERE id != '$id' && sort_order >= $sort_order && product_id=$product_id") or die(mysqli_error($this->con));
        }
    }
    // Box
    function select_max_sort_order_box($product_id) {
        $result = $this->con->query("select max(sort_order) as sort_order from box_image where product_id = $product_id ");
        $row = mysqli_fetch_assoc($result);
        return $row['sort_order'];
    }

	function escape($string){
		return mysqli_real_escape_string($this->con, trim($string));
	}

    function add_box($product_id, $caption, $img, $sort_order) {
        $caption = mysqli_real_escape_string($this->con, $caption);
        $result = $this->con->query("insert into box_image (product_id, caption,img, sort_order) values ($product_id, '$caption', '$img',  $sort_order)") or die(mysqli_error($this->con));
        if ($result) $this->update_box_sort_order($product_id, $sort_order, mysqli_insert_id($this->con));
    }
    function update_box($product_id, $caption, $img, $sort_order, $id) {
        $caption = mysqli_real_escape_string($this->con, $caption);
        $result = $this->con->query("update box_image set product_id = $product_id, caption = '$caption', img='$img' details = '$details', sort_order = $sort_order where id=$id") or die(mysqli_error($this->con));
        if ($result) $this->update_specs_sort_order($product_id, $sort_order, $id);
    }
    function update_box_sort_order($product_id, $sort_order, $id) {
        $result = $this->con->query("select * from box_image where  product_id=$product_id && sort_order = $sort_order ") or die(mysqli_error($this->con));
        if (mysqli_num_rows($result) > 1) { // check if dufplicate
            $this->con->query("UPDATE box_image SET sort_order = $sort_order +1 WHERE id != '$id' && sort_order >= $sort_order && product_id=$product_id") or die(mysqli_error($this->con));
        }
    }
    // highlights
    function add_highlites($product_id, $heading, $demonstration, $product, $icon, $sort_order) {
        $result = $this->con->query("insert into highlights (product_id, heading, demonstration, product, icon,  sort_order ) values ('$product_id', '$heading', '$demonstration', '$product','$icon',$sort_order)") or die(mysqli_error($this->con));
        if ($result) $this->update_highlites_sort_order($sort_order, mysqli_insert_id($this->con));
    }
    function update_highlites($product_id, $heading, $demonstration, $product, $icon, $sort_order, $id) {
        $result = $this->con->query("update highlights SET product_id = $product_id, heading='$heading', demonstration='$demonstration', product='$product', icon='$icon',  sort_order=$sort_order where id = $id ") or die(mysqli_error($this->con));
        if ($result) $this->update_highlites_sort_order($sort_order, $id);
    }
    function update_highlites_sort_order($sort_order, $id) {
        $result = $this->con->query("select * from highlights where  sort_order = $sort_order ") or die(mysqli_error($this->con));
        if (mysqli_num_rows($result) > 1) { // check if dufplicate
            $this->con->query("UPDATE highlights SET sort_order = $sort_order +1 WHERE id != $id ") or die(mysqli_error($this->con));
        }
    }
    function select_all_highlites() {
        $result = $this->con->query("select highlights.*, products.name as product_name from highlights
		left join products ON products.id = highlights.product_id
		order by highlights.product_id, highlights.sort_order") or die(mysqli_error($this->con));
        return $result;
    }
    function select_all_active_highlites() {
        $result = $this->con->query("select highlights.*, products.name as product_name,  division.name as division_name from highlights
		left join products ON products.id = highlights.product_id
		left join division ON division.id = products.division_id
		where highlights.status=1
		order by highlights.product_id, highlights.sort_order ") or die(mysqli_error($this->con));
        return $result;
    }

	function related_item_product($product_id=0, $division_id=0, $main_division_id=0){

	    $sql1 = "SELECT m.*, t.name as division, d.name as main_division
		        FROM products as m
		            left JOIN  division as t ON m.division_id=t.id   left JOIN  main_division as d ON m.main_division_id=d.id
				WHERE 1=1 AND m.status=1   AND m.id!='$product_id'";
		if($division_id>0){
			$sql1.= " AND t.status=1 AND t.id='$division_id'  GROUP BY m.id ORDER BY RAND() LIMIT 10";
		}else{
			$sql1.= " AND d.status=1 AND d.id='$main_division_id'  GROUP BY m.id ORDER BY RAND() LIMIT 10";
		}

		$result = $this->con->query($sql1);
		return $result;

	}

    // highlite feture
    function select_max_sort_order_highlite_feture($highlights_id) {
        $result = $this->con->query("select max(sort_order) as sort_order from highlights_features where highlights_id = $highlights_id ");
        $row = mysqli_fetch_assoc($result);
        return $row['sort_order'];
    }
    function add_highlite_feture($highlights_id, $type, $columns, $icon, $heading, $content, $sort_order) {
        $result = $this->con->query("insert into highlights_features (highlights_id, type, columns, icon,  heading, content,  sort_order) values ($highlights_id, $type, $columns, '$icon', '$heading', '$content',  $sort_order)") or die(mysqli_error($this->con));
        if ($result) $this->update_highlite_feture_sort_order($highlights_id, $sort_order, mysqli_insert_id($this->con));
    }
    function update_highlite_feture($highlights_id, $type, $columns, $icon, $heading, $content, $sort_order, $id) {
        $result = $this->con->query("update highlights_features set highlights_id = $highlights_id, type='$type',columns='$columns',icon='$icon', heading = '$heading', content = '$content',  sort_order = $sort_order where id=$id") or die(mysqli_error($this->con));
        if ($result) $this->update_highlite_feture_sort_order($highlights_id, $sort_order, $id);
    }
    function update_highlite_feture_sort_order($highlights_id, $sort_order, $id) {
        $result = $this->con->query("select * from highlights_features where  highlights_id=$highlights_id && sort_order = $sort_order ") or die(mysqli_error($this->con));
        if (mysqli_num_rows($result) > 1) { // check if dufplicate
            $this->con->query("UPDATE highlights_features SET sort_order = $sort_order +1 WHERE id != '$id' && sort_order >= $sort_order && highlights_id=$highlights_id") or die(mysqli_error($this->con));
        }
    }

  public function active_countries(){
	   $sql = "SELECT c.*  FROM locations  as l  JOIN  country as c ON c.id=l.country GROUP BY c.id";
		return $result = $this->con->query($sql);
  }

  public function select_all_stores($where){
	     $sql = "SELECT l.*, c.name as country_name  FROM locations  as l  JOIN  country as c ON c.id=l.country $where";
		return $result = $this->con->query($sql);
  }
    function insert_data($table_name, $form_data) {
        // retrieve the keys of the array (column titles)
        $fields = array_keys($form_data);
        // build the query
        $sql = "INSERT INTO " . $table_name . "

		(`" . implode('`,`', $fields) . "`)

		VALUES('" . implode("','", $form_data) . "')";
        // run and return the query result resource
    $this->con->query($sql) or die(mysqli_error($this->con));
        return mysqli_insert_id($this->con);
    }
    // again where clause is left optional
    function update_data($table_name, $form_data, $id = '') {
        // check for optional where clause
        $whereSQL = '';
        if (!empty($id)) {
            $whereSQL = " WHERE id=" . $id;
        }
        // start the actual SQL statement
        $sql = "UPDATE " . $table_name . " SET ";
        // loop and build the column /
        $sets = array();
        foreach ($form_data as $column => $value) {
             $sets[] = "`" . $column . "` = '" . $value . "'";
        }
        $sql.= implode(', ', $sets);
        // append the where statement
        $sql.= $whereSQL;
        // run and return the query result
        $this->con->query($sql) or die(mysqli_error($this->con));
        return $id;
    }
    function random_home() {
        $result = $this->con->query("SELECT * FROM `app_home` WHERE status=1 ORDER BY RAND() LIMIT 1");
        $row = mysqli_fetch_assoc($result);
        return $row;
    }
    function select_product_by_filter($search = array()) {
        $tag = "";
        if (isset($search['search_tag'])) {
            $tag = mysqli_real_escape_string($this->con, $search['search_tag']);
            unset($search['search_tag']);
        }
        $sql = "SELECT m.*, t.name as type_name, d.name as division_name FROM products as m JOIN  division as t ON m.division_id=t.id JOIN main_division AS d ON d.id=t.main_id WHERE 1=1 ";
        foreach ($search as $field => $value) {
            $sql.= " AND " . $field . "=" . $value . " ";
        }
        if ($tag != "") {
            $sql.= " AND (m.name LIKE '%" . $tag . "%' OR  m.title LIKE '%" . $tag . "%'   OR  m.sub_title LIKE '%" . $tag . "%'  OR  m.features  LIKE '%" . $tag . "%'  OR t.name LIKE '%" . $tag . "%'  OR d.name LIKE '%" . $tag . "%'  )";
        }
        $sql.= " AND m.status=1";
        $result = $this->con->query($sql);
        return $result;
    }
    function select_product_type_except_by_filter($search = array()) {
        $tag = "";
        if (isset($search['search_tag'])) {
            $tag = mysqli_real_escape_string($this->con, $search['search_tag']);
            unset($search['search_tag']);
        }
        $sql = "SELECT m.*, t.name as type_name, d.name as division_name FROM products as m JOIN  division as t ON m.division_id=t.id JOIN main_division AS d ON d.id=t.main_id WHERE 1=1 ";
        foreach ($search as $field => $value) {
            if ($field == 'division_id') {
                $sql.= " AND " . $field . "!=" . $value . " ";
            } else {
                $sql.= " AND " . $field . "=" . $value . " ";
            }
        }
        if ($tag != "") {
            $sql.= " AND (m.name LIKE '%" . $tag . "%' OR  m.title LIKE '%" . $tag . "%'   OR  m.sub_title LIKE '%" . $tag . "%'  OR  m.features  LIKE '%" . $tag . "%'  OR t.name LIKE '%" . $tag . "%'  OR d.name LIKE '%" . $tag . "%'  )";
        }
        $sql.= " AND m.status=1";
        $result = $this->con->query($sql);
        return $result;
    }
    function login($email, $password) {
        $result = $this->con->query("select * from user where email='" . $email . "' AND  password='" . $password . "' AND status=1 LIMIT 1") or die(mysqli_error($this->con));
        $row = mysqli_fetch_assoc($result);
        if (mysqli_num_rows($result) > 0) {
            return $row;
        } else {
            return false;
        }
    }
    function check_userExists($email, $phone) {
        $result = $this->con->query("select * from user where email='" . $email . "' OR  phone='" . $phone . "'") or die(mysqli_error($this->con));
        $cnt = mysqli_num_rows($result);
        return $cnt;
    }
    function send_credentils($email) {
        $result = $this->con->query("select * from user where email='" . $email . "' LIMIT 1") or die(mysqli_error($this->con));
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $subject = "HEATZ login credentials";
            $message = "Hi " . $row['name'] . "<br/>";
            $message.= "Thank you for touch with us, your login credentials are below<br/><br/>";
            $message.= "<b>Email:</b> " . $row['email'] . "<br/>";
            $message.= "<b>Password:</b> " . $row['password'] . "<br/>";
            // Always set content-type when sending HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers.= "Content-type:text/html;charset=UTF-8" . "\r\n";
            // More headers
            $headers.= 'From: HEATZ<info@heatz.store>' . "\r\n";
            if (mail($email, $subject, $message, $headers)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    function select_product_by_array($ids) {
        $sql = "SELECT m.*, t.name as type_name, d.name as division_name FROM products as m JOIN  division as t ON m.division_id=t.id JOIN main_division AS d ON d.id=t.main_id WHERE 1=1 ";
        $sql.= " AND m.id IN (" . implode(', ', $ids) . ")";
        $sql.= " AND m.status=1";
        $result = $this->con->query($sql);
        return $result;
    }
    function select_bd_countries() {
        $sql = "select country.id as id, country.name as name FROM  country order by id";
        $result = $this->con->query($sql);
        return $result;
    }
    function select_all_catalogues($where) {
        $sql = "SELECT C.*, Cn.name as country, u.username FROM `catalog_basic` as C JOIN country as Cn ON C.country_id=Cn.id JOIN catalog_user as u ON C.added_by=u.id  WHERE $where";
        $result = mysqli_query($this->con, $sql);
        return $result;
    }


	function fetch_models(){
		$date = date("Y-m-d");
		$sql = "SELECT PZ.*, D.name as division FROM `product_price` as PZ JOIN products as P ON PZ.model=P.name JOIN main_division AS D ON P.main_division_id=D.id
		 WHERE PZ.id=(select pz2.id from product_price as pz2 where pz2.date_added<='$date' AND pz2.model = PZ.model AND pz2.status=1 order by pz2.date_added desc limit 0,1)   AND PZ.status=1 group by PZ.model ORDER BY PZ.date_added desc";
		$result = $this->con->query($sql);
        return $result;

	}

	function fetch_models_by_division($division_id=0){
		$date = date("Y-m-d");
		$sql = "SELECT PZ.*, D.name as division FROM `product_price` as PZ JOIN products as P ON PZ.model=P.name JOIN main_division AS D ON P.main_division_id=D.id
		 WHERE PZ.id=(select pz2.id from product_price as pz2 where pz2.date_added<='$date' AND pz2.model = PZ.model AND pz2.status=1 order by pz2.date_added desc limit 0,1)   AND PZ.status=1 AND D.id='$division_id' ORDER BY P.division_id, P.name";
		$result = $this->con->query($sql);
        return $result;

	}


	function update_price_status($model_id='', $date='0000-00-00'){
		 $sql = "UPDATE product_price SET status=0 WHERE model='$model_id' AND date_added='$date'";
        $result = mysqli_query($this->con, $sql);

	}

	function select_products_selected($where="1=1"){
		$sql = "SELECT D.*, P.* FROM `catalog_details` as D JOIN products as P ON D.model_name=P.name  WHERE $where";
		$result = mysqli_query($this->con, $sql);
		return $result;
	}

	function select_pricing_by_model($model,$basic_id){

		$sql = "SELECT D.* FROM `catalog_details` as D   WHERE D.model_name LIKE '$model%' AND basic_id='$basic_id' AND D.status=1";
		$result = mysqli_query($this->con, $sql);
		if($result){
			$arr = array();
			while($row = mysqli_fetch_assoc($result)){
					 $arr[] = $row;
			}


			return $arr;
		}

    }


    // MISBAH

    function rename_file($upload_folder, $file_name)
    {

        $file_name = preg_replace('/\s+/', '_', $file_name);
        $actual_name = pathinfo($file_name, PATHINFO_FILENAME);
        $original_name = $actual_name;
        $extension = pathinfo($file_name, PATHINFO_EXTENSION);

        $j = 1;
        while (file_exists($upload_folder . $actual_name . "." . $extension)) {
            $actual_name = (string) $original_name . "_" . $j;
            $file_name = $actual_name . "." . $extension;
            $j++;
        }
        return $file_name;
    }



    function cloud_login($username, $password){
        // die("Select user.*, branch.name as branch_name from cloud_user as user
        // left join branch on branch.id = user.branch_id
        // where user.username='$username' && user.password ='$password' && user.status = 1 ");
        $result = $this->con->query("Select user.*, branch.name as branch_name from cloud_user as user
        left join branch on branch.id = user.branch_id
        where user.username='$username' && user.password ='$password' && user.status = 1 ") or die(mysqli_error($this->con));

            return mysqli_fetch_assoc($result);
    }

    function select_all_cloud_user(){
        $result = $this->con->query("select user.*, branch.name as branch_name, type.name as user_type_name
                                    from cloud_user as user
                                    left join branch on branch.id = user.branch_id
                                    left join cloud_user_type as type on type.id = user.user_type");
        return $result;
    }

    function select_all_branch(){
        $result = $this->con->query("select branch.*, country.name as country_name
                                    from branch
                                    left join country on country.id = branch.country_id
                                    order by country.id
                                    ");
                  return $result;
    }

    function select_country_having_branch(){
        $result = $this->con->query("select country.id, country.name from branch
                            left join country on branch.country_id = country.id
                            where branch.status = 1
                            group by country.id
                        ");

        return $result;
    }
    function select_branch_by_country($id){
        $result = $this->con->query("select branch.id, branch.name from branch
                            where branch.status = 1 && branch.country_id = $id
                        ");

        return $result;
    }

    function select_id_by_model_name($name){

        $result = $this->con->query("select id from products where name='$name' ") or die(mysqli_error($this->con));
        $row = mysqli_fetch_assoc($result);
        if($row)
            return $row['id'];
        return false;
    }

    function select_rma_batch(){
        $result  = $this->con->query("select rma.*, branch.name as branch_name  from cloud_rma as rma
                                     left join branch On branch.id = rma.branch_id
                                     order by rma.id desc
                                    ") or die(mysqli_error($this->con));
        return $result;
    }

		function select_sales_batch($branch){
        $result  = $this->con->query("select sales.*, branch.name as branch_name from cloud_sales as sales left join branch On branch.id = sales.selected_branch where sales.branch_id=$branch order by sales.id desc
                                    ") or die(mysqli_error($this->con));
        return $result;
    }

				function select_sales_batch_by_admin(){
		        $result  = $this->con->query("select sales.*, branch.name as branch_name from cloud_sales as sales left join branch On branch.id = sales.selected_branch order by sales.id desc
		                                    ") or die(mysqli_error($this->con));
		        return $result;
		    }

		function select_receaved_rma_count($id){
				$result  = $this->con->query("select count(id) as count from  cloud_rma_list where rma_id=$id") or die(mysqli_error($this->con));
				$row = mysqli_fetch_assoc($result);
				if($row)
						return $row['count'];
				return false;
		}
		function select_requested_count($id){
				$result  = $this->con->query("select sum(quantity) as count from  cloud_rma_verify where rma_id=$id") or die(mysqli_error($this->con));
				$row = mysqli_fetch_assoc($result);
				if($row)
						return $row['count'];
				return false;
		}

		function select_cleared_rma_count($id){
				$result  = $this->con->query("select count(id) as count from  cloud_rma_list where rma_id=$id AND status=2") or die(mysqli_error($this->con));
				$row = mysqli_fetch_assoc($result);
				if($row)
						return $row['count'];
				return false;
		}
		function select_rejected_rma_count($id){
				$result  = $this->con->query("select count(id) as count from  cloud_rma_list where rma_id=$id AND status=0") or die(mysqli_error($this->con));
				$row = mysqli_fetch_assoc($result);
				if($row)
						return $row['count'];
				return false;
		}

    function select_rma_batch_by_id($id){
        $result  = $this->con->query("select rma.*, branch.name as branch_name from cloud_rma as rma
                                     left join branch On branch.id = rma.branch_id
                                     where rma.id = $id
                                    ") or die(mysqli_error($this->con));
        return mysqli_fetch_assoc($result);
    }
		function select_sales_batch_by_id($id){
        $result  = $this->con->query("select sales.*, branch.name as branch_name from cloud_sales as sales left join branch On branch.id = sales.selected_branch where sales.id =$id
                                    ") or die(mysqli_error($this->con));
        return mysqli_fetch_assoc($result);
    }

    function select_all_rma_list_by_parent($rma_id){
        $result = $this->con->query("select list.*, products.name as model_name, branch.name as branch_name
                                    from cloud_rma_list as list
                                     left join cloud_rma as rma ON rma.id = list.rma_id
                                     left join products On products.id = list.model_id
                                     left join branch On branch.id = rma.branch_id
                                     where rma.id = $rma_id
                                     order by rma.id, list.model_id
            ") or die(mysqli_error($this->con));
        return $result;
    }
		function select_all_sales_list_by_parent($sales_id){
				$result = $this->con->query("select list.*, products.name as model_name, branch.name as branch_name from cloud_sales_list as list left join cloud_sales as sales ON sales.id = list.sales_id left join products On products.id = list.model_id left join branch On branch.id = sales.branch_id where sales.id = $sales_id order by sales.id, list.model_id
						") or die(mysqli_error($this->con));
				return $result;
		}

    function select_all_rma_list(){
        $result = $this->con->query("select list.*, products.name as model_name, branch.name as branch_name
                                    from cloud_rma_list as list
                                     left join cloud_rma as rma ON rma.id = list.rma_id
                                     left join products On products.id = list.model_id
                                     left join branch On branch.id = rma.branch_id
                                     order by rma.id, list.model_id
            ") or die(mysqli_error($this->con));
        return $result;
    }

		function select_all_product_report()
		{
			$result = $this->con->query("select * from  products") or die(mysqli_error($this->con));
			return $result;
		}
		function select_rma_verify($rmaid)
		{
			$result = $this->con->query("SELECT *,cloud_rma_verify.id as cid FROM cloud_rma_verify LEFT JOIN products on products.id = cloud_rma_verify.model_id LEFT JOIN cloud_rma on cloud_rma.id=cloud_rma_verify.rma_id WHERE cloud_rma_verify.rma_id=$rmaid") or die(mysqli_error($this->con));
			return $result;
		}
		function select_all_country()
		{
			$result = $this->con->query("select * from  country") or die(mysqli_error($this->con));
			return $result;
		}

		function checkreadyprocess($id)
		{
			$result = $this->con->query("select count(rma_id) as count from cloud_rma_list where rma_id=$id AND status=1 OR status=3") or die(mysqli_error($this->con));
			$row = mysqli_fetch_assoc($result);
			if($row)
					return $row['count'];
			return false;
		}
		function checkreadyprocessing($id)
		{
			$result = $this->con->query("select count(rma_id) as count from cloud_rma_list where rma_id=$id AND (status=0 OR status=2)") or die(mysqli_error($this->con));
			$row = mysqli_fetch_assoc($result);
			if($row)
					return $row['count'];
			return false;
		}
		function checkverifyquantity($id)
		{
			$result = $this->con->query("select count(rma_id) as count from cloud_rma_list where rma_id=$id AND quantity=1") or die(mysqli_error($this->con));
			$row = mysqli_fetch_assoc($result);
			if($row)
					return $row['count'];
			return false;
		}
		function checkdefaultverify($id)
		{
			$result = $this->con->query("select count(id) as count from  cloud_rma where id=$id AND verify=1") or die(mysqli_error($this->con));
			$row = mysqli_fetch_assoc($result);
			if($row)
					return $row['count'];
			return false;
		}




		function totalsalesbybranchbymonth($branch,$model,$year,$month,$country)
				{
					$result = $this->con->query("select sum(quantity) as count from  cloud_sales_list as sales_list
					 LEFT JOIN cloud_sales as sales ON sales.id=sales_list.sales_id
					 LEFT JOIN branch ON branch.id=sales.selected_branch

					 where sales.branch_id=".$branch."
					 AND sales_list.model_id=$model AND sales.year=$year AND sales.month=$month AND branch.country_id=$country") or die(mysqli_error($this->con));
					$row = mysqli_fetch_assoc($result);
					if($row['count']!=0)
					{
						return $row['count'];
					}
					else {
						return 0;
					}

				}

				function totalsalesbybranch($branch,$model,$year,$month,$country)
								{
									$result = $this->con->query("select sum(quantity) as count from  cloud_sales_list as sales_list
									 LEFT JOIN cloud_sales as sales ON sales.id=sales_list.sales_id
									 LEFT JOIN branch ON branch.id=sales.selected_branch

									 where sales.selected_branch=".$branch."
									 AND sales_list.model_id=$model") or die(mysqli_error($this->con));
									$row = mysqli_fetch_assoc($result);
									if($row['count']!=0)
									{
										return $row['count'];
									}
									else {
										return 0;
									}

								}


				function totalonesidenotworking($model,$branch)
								{
									$result = $this->con->query("select sum(quantity) as count from cloud_rma_list as rma_list left join cloud_rma on cloud_rma.id=rma_list.rma_id where rma_list.model_id=$model AND rma_list.compliant_id=1 AND cloud_rma.branch_id=$branch
				") or die(mysqli_error($this->con));
									$row = mysqli_fetch_assoc($result);
									if($row['count']!=0)
									{
										return $row['count'];
									}
									else {
										return 0;
									}

								}
								function totalsounddefective($model,$branch)
								{
									$result = $this->con->query("select sum(quantity) as count from cloud_rma_list as rma_list left join cloud_rma on cloud_rma.id=rma_list.rma_id where rma_list.model_id=$model AND rma_list.compliant_id=2 AND cloud_rma.branch_id=$branch
				") or die(mysqli_error($this->con));
									$row = mysqli_fetch_assoc($result);
									if($row['count']!=0)
									{
										return $row['count'];
									}
									else {
										return 0;
									}

								}
								function totalmicnotworking($model,$branch)
								{
									$result = $this->con->query("select sum(quantity) as count from cloud_rma_list as rma_list left join cloud_rma on cloud_rma.id=rma_list.rma_id where rma_list.model_id=$model AND rma_list.compliant_id=3 AND cloud_rma.branch_id=$branch
				") or die(mysqli_error($this->con));
									$row = mysqli_fetch_assoc($result);
									if($row['count']!=0)
									{
										return $row['count'];
									}
									else {
										return 0;
									}

								}
								function totalcabledefective($model,$branch)
								{
									$result = $this->con->query("select sum(quantity) as count from cloud_rma_list as rma_list left join cloud_rma on cloud_rma.id=rma_list.rma_id where rma_list.model_id=$model AND rma_list.compliant_id=4 AND cloud_rma.branch_id=$branch
				") or die(mysqli_error($this->con));
									$row = mysqli_fetch_assoc($result);
									if($row['count']!=0)
									{
										return $row['count'];
									}
									else {
										return 0;
									}

								}
								function totalbatteryissues($model,$branch)
								{
									$result = $this->con->query("select sum(quantity) as count from cloud_rma_list as rma_list left join cloud_rma on cloud_rma.id=rma_list.rma_id where rma_list.model_id=$model AND rma_list.compliant_id=5 AND cloud_rma.branch_id=$branch
				") or die(mysqli_error($this->con));
									$row = mysqli_fetch_assoc($result);
									if($row['count']!=0)
									{
										return $row['count'];
									}
									else {
										return 0;
									}

								}
								function totalnotcharging($model,$branch)
								{
									$result = $this->con->query("select sum(quantity) as count from cloud_rma_list as rma_list left join cloud_rma on cloud_rma.id=rma_list.rma_id where rma_list.model_id=$model AND rma_list.compliant_id=7 AND cloud_rma.branch_id=$branch
				") or die(mysqli_error($this->con));
									$row = mysqli_fetch_assoc($result);
									if($row['count']!=0)
									{
										return $row['count'];
									}
									else {
										return 0;
									}

								}
								function totalheat($model,$branch)
								{
									$result = $this->con->query("select sum(quantity) as count from cloud_rma_list as rma_list left join cloud_rma on cloud_rma.id=rma_list.rma_id where rma_list.model_id=$model AND rma_list.compliant_id=8 AND cloud_rma.branch_id=$branch
				") or die(mysqli_error($this->con));
									$row = mysqli_fetch_assoc($result);
									if($row['count']!=0)
									{
										return $row['count'];
									}
									else {
										return 0;
									}

								}
								function totalphysicaldamage($model,$branch)
								{
									$result = $this->con->query("select sum(quantity) as count from cloud_rma_list as rma_list left join cloud_rma on cloud_rma.id=rma_list.rma_id where rma_list.model_id=$model AND rma_list.compliant_id=9 AND cloud_rma.branch_id=$branch
				") or die(mysqli_error($this->con));
									$row = mysqli_fetch_assoc($result);
									if($row['count']!=0)
									{
										return $row['count'];
									}
									else {
										return 0;
									}

								}
								function totalunnonissues($model,$branch)
								{
									$result = $this->con->query("select sum(quantity) as count from cloud_rma_list as rma_list left join cloud_rma on cloud_rma.id=rma_list.rma_id where rma_list.model_id=$model AND rma_list.compliant_id=11 AND cloud_rma.branch_id=$branch
				") or die(mysqli_error($this->con));
									$row = mysqli_fetch_assoc($result);
									if($row['count']!=0)
									{
										return $row['count'];
									}
									else {
										return 0;
									}

								}
								function totalboardcomplaints($model,$branch)
								{
									$result = $this->con->query("select sum(quantity) as count from cloud_rma_list as rma_list left join cloud_rma on cloud_rma.id=rma_list.rma_id where rma_list.model_id=$model AND rma_list.compliant_id=12 AND cloud_rma.branch_id=$branch
				") or die(mysqli_error($this->con));
									$row = mysqli_fetch_assoc($result);
									if($row['count']!=0)
									{
										return $row['count'];
									}
									else {
										return 0;
									}

								}





				function select_country_by_branch($branch)
				{
					$result = $this->con->query("select country_id as country_id from  branch where id=$branch") or die(mysqli_error($this->con));
					$row = mysqli_fetch_assoc($result);
					if($row['country_id'])
					{
						return $row['country_id'];
					}

				}

// filter

function totalsalesbybranchfilter($branch=null,$model,$year=null,$month=null,$country=null)
{

	$sql="select sum(quantity) as count from  cloud_sales_list as sales_list
	 LEFT JOIN cloud_sales as sales ON sales.id=sales_list.sales_id
	 LEFT JOIN branch ON branch.id=sales.selected_branch

	 where sales_list.model_id=$model";
if($branch)
{
	 $sql.=" AND sales.selected_branch=$branch";
}
if($year)
{
	 $sql.=" AND sales.year=$year";
}

if($month)
{
	 $sql.=" AND sales.month=$month";
}


if($country)
{
	 $sql.=" AND branch.country_id=$country";
}



	$result = $this->con->query($sql) or die(mysqli_error($this->con));
	$row = mysqli_fetch_assoc($result);
	if($row['count']!=0)
	{
		return $row['count'];
	}
	else {
		return 0;
	}

}

function totalonesidenotworkingfilter($model,$branch=null,$year=null,$month=null,$country=null)
{

	$sql="select sum(quantity) as count from cloud_rma_list as rma_list
	left join cloud_rma on cloud_rma.id=rma_list.rma_id
	left join branch on branch.id=cloud_rma.branch_id
	where rma_list.model_id=$model AND rma_list.compliant_id=1";
	if($branch)
	{
		 $sql.=" AND cloud_rma.branch_id=$branch";
	}
	if($year)
	{
		 $sql.=" AND YEAR(rma_list.added_at) =$year";
	}

	if($month)
	{
		 $sql.=" AND MONTH(rma_list.added_at)=$branch";
	}


	if($country)
	{
		 $sql.=" AND branch.country_id=$country";
	}

	$result = $this->con->query($sql) or die(mysqli_error($this->con));
	$row = mysqli_fetch_assoc($result);
	if($row['count']!=0)
	{
		return $row['count'];
	}
	else {
		return 0;
	}

}
function totalsounddefectivefilter($model,$branch=null,$year=null,$month=null,$country=null)
{
	$sql="select sum(quantity) as count from cloud_rma_list as rma_list
	left join cloud_rma on cloud_rma.id=rma_list.rma_id
	left join branch on branch.id=cloud_rma.branch_id
	where rma_list.model_id=$model AND rma_list.compliant_id=2";
	if($branch)
	{
		 $sql.=" AND cloud_rma.branch_id=$branch";
	}
	if($year)
	{
		 $sql.=" AND YEAR(rma_list.added_at) =$year";
	}

	if($month)
	{
		 $sql.=" AND MONTH(rma_list.added_at)=$branch";
	}


	if($country)
	{
		 $sql.=" AND branch.country_id=$country";
	}
	$result = $this->con->query($sql) or die(mysqli_error($this->con));
	$row = mysqli_fetch_assoc($result);
	if($row['count']!=0)
	{
		return $row['count'];
	}
	else {
		return 0;
	}

}
function totalmicnotworkingfilter($model,$branch=null,$year=null,$month=null,$country=null)
{
	$sql="select sum(quantity) as count from cloud_rma_list as rma_list
	left join cloud_rma on cloud_rma.id=rma_list.rma_id
	left join branch on branch.id=cloud_rma.branch_id
	where rma_list.model_id=$model AND rma_list.compliant_id=3";
	if($branch)
	{
		 $sql.=" AND cloud_rma.branch_id=$branch";
	}
	if($year)
	{
		 $sql.=" AND YEAR(rma_list.added_at) =$year";
	}

	if($month)
	{
		 $sql.=" AND MONTH(rma_list.added_at)=$branch";
	}


	if($country)
	{
		 $sql.=" AND branch.country_id=$country";
	}
	$result = $this->con->query($sql) or die(mysqli_error($this->con));
	$row = mysqli_fetch_assoc($result);
	if($row['count']!=0)
	{
		return $row['count'];
	}
	else {
		return 0;
	}

}
function totalcabledefectivefilter($model,$branch=null,$year=null,$month=null,$country=null)
{
	$sql="select sum(quantity) as count from cloud_rma_list as rma_list
	left join cloud_rma on cloud_rma.id=rma_list.rma_id
	left join branch on branch.id=cloud_rma.branch_id
	where rma_list.model_id=$model AND rma_list.compliant_id=4";
	if($branch)
	{
		 $sql.=" AND cloud_rma.branch_id=$branch";
	}
	if($year)
	{
		 $sql.=" AND YEAR(rma_list.added_at) =$year";
	}

	if($month)
	{
		 $sql.=" AND MONTH(rma_list.added_at)=$branch";
	}


	if($country)
	{
		 $sql.=" AND branch.country_id=$country";
	}
	$result = $this->con->query($sql) or die(mysqli_error($this->con));
	$row = mysqli_fetch_assoc($result);
	if($row['count']!=0)
	{
		return $row['count'];
	}
	else {
		return 0;
	}

}
function totalbatteryissuesfilter($model,$branch=null,$year=null,$month=null,$country=null)
{
	$sql="select sum(quantity) as count from cloud_rma_list as rma_list
	left join cloud_rma on cloud_rma.id=rma_list.rma_id
	left join branch on branch.id=cloud_rma.branch_id
	where rma_list.model_id=$model AND rma_list.compliant_id=5";
	if($branch)
	{
		 $sql.=" AND cloud_rma.branch_id=$branch";
	}
	if($year)
	{
		 $sql.=" AND YEAR(rma_list.added_at) =$year";
	}

	if($month)
	{
		 $sql.=" AND MONTH(rma_list.added_at)=$branch";
	}


	if($country)
	{
		 $sql.=" AND branch.country_id=$country";
	}
	$result = $this->con->query($sql) or die(mysqli_error($this->con));
	$row = mysqli_fetch_assoc($result);
	if($row['count']!=0)
	{
		return $row['count'];
	}
	else {
		return 0;
	}

}
function totalnotchargingfilter($model,$branch=null,$year=null,$month=null,$country=null)
{
	$sql="select sum(quantity) as count from cloud_rma_list as rma_list
	left join cloud_rma on cloud_rma.id=rma_list.rma_id
	left join branch on branch.id=cloud_rma.branch_id
	where rma_list.model_id=$model AND rma_list.compliant_id=7";
	if($branch)
	{
		 $sql.=" AND cloud_rma.branch_id=$branch";
	}
	if($year)
	{
		 $sql.=" AND YEAR(rma_list.added_at) =$year";
	}

	if($month)
	{
		 $sql.=" AND MONTH(rma_list.added_at)=$branch";
	}


	if($country)
	{
		 $sql.=" AND branch.country_id=$country";
	}
	$result = $this->con->query($sql) or die(mysqli_error($this->con));
	$row = mysqli_fetch_assoc($result);
	if($row['count']!=0)
	{
		return $row['count'];
	}
	else {
		return 0;
	}

}
function totalheatfilter($model,$branch=null,$year=null,$month=null,$country=null)
{
	$sql="select sum(quantity) as count from cloud_rma_list as rma_list
	left join cloud_rma on cloud_rma.id=rma_list.rma_id
	left join branch on branch.id=cloud_rma.branch_id
	where rma_list.model_id=$model AND rma_list.compliant_id=8";
	if($branch)
	{
		 $sql.=" AND cloud_rma.branch_id=$branch";
	}
	if($year)
	{
		 $sql.=" AND YEAR(rma_list.added_at) =$year";
	}

	if($month)
	{
		 $sql.=" AND MONTH(rma_list.added_at)=$branch";
	}


	if($country)
	{
		 $sql.=" AND branch.country_id=$country";
	}
	$result = $this->con->query($sql) or die(mysqli_error($this->con));
	$row = mysqli_fetch_assoc($result);
	if($row['count']!=0)
	{
		return $row['count'];
	}
	else {
		return 0;
	}

}
function totalphysicaldamagefilter($model,$branch=null,$year=null,$month=null,$country=null)
{
	$sql="select sum(quantity) as count from cloud_rma_list as rma_list
	left join cloud_rma on cloud_rma.id=rma_list.rma_id
	left join branch on branch.id=cloud_rma.branch_id
	where rma_list.model_id=$model AND rma_list.compliant_id=9";
	if($branch)
	{
		 $sql.=" AND cloud_rma.branch_id=$branch";
	}
	if($year)
	{
		 $sql.=" AND YEAR(rma_list.added_at) =$year";
	}

	if($month)
	{
		 $sql.=" AND MONTH(rma_list.added_at)=$branch";
	}


	if($country)
	{
		 $sql.=" AND branch.country_id=$country";
	}
	$result = $this->con->query($sql) or die(mysqli_error($this->con));
	$row = mysqli_fetch_assoc($result);
	if($row['count']!=0)
	{
		return $row['count'];
	}
	else {
		return 0;
	}

}
function totalunnonissuesfilter($model,$branch=null,$year=null,$month=null,$country=null)
{
	$sql="select sum(quantity) as count from cloud_rma_list as rma_list
	left join cloud_rma on cloud_rma.id=rma_list.rma_id
	left join branch on branch.id=cloud_rma.branch_id
	where rma_list.model_id=$model AND rma_list.compliant_id=11";
	if($branch)
	{
		 $sql.=" AND cloud_rma.branch_id=$branch";
	}
	if($year)
	{
		 $sql.=" AND YEAR(rma_list.added_at) =$year";
	}

	if($month)
	{
		 $sql.=" AND MONTH(rma_list.added_at)=$branch";
	}


	if($country)
	{
		 $sql.=" AND branch.country_id=$country";
	}
	$result = $this->con->query($sql) or die(mysqli_error($this->con));
	$row = mysqli_fetch_assoc($result);
	if($row['count']!=0)
	{
		return $row['count'];
	}
	else {
		return 0;
	}

}
function totalboardcomplaintsfilter($model,$branch=null,$year=null,$month=null,$country=null)
{
	$sql="select sum(quantity) as count from cloud_rma_list as rma_list
	left join cloud_rma on cloud_rma.id=rma_list.rma_id
	left join branch on branch.id=cloud_rma.branch_id
	where rma_list.model_id=$model AND rma_list.compliant_id=12";
	if($branch)
	{
		 $sql.=" AND cloud_rma.branch_id=$branch";
	}
	if($year)
	{
		 $sql.=" AND YEAR(rma_list.added_at) =$year";
	}

	if($month)
	{
		 $sql.=" AND MONTH(rma_list.added_at)=$branch";
	}


	if($country)
	{
		 $sql.=" AND branch.country_id=$country";
	}
	$result = $this->con->query($sql) or die(mysqli_error($this->con));
	$row = mysqli_fetch_assoc($result);
	if($row['count']!=0)
	{
		return $row['count'];
	}
	else {
		return 0;
	}

}



}
