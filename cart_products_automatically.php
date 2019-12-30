<?php

/*
Plugin Name: Cart Products Automatically
Plugin URI:https://localhost/wordpress_shas/wordpress/wp-admin/plugins/cart_products_automatically
Description: A plugin which add products to the cart automatically when the given conditions comes true.
Version: 0.1.1
Requires at least: 5.2
Requires PHP:4.3
Author: Shasvat Shah
Author URI:https://www.shasvat.com
Text Domain:cart-products-automatically
Domain Path: /languages/
*/



/**
 *Exit if accessed directly
 * 
 */
defined( 'ABSPATH' ) || exit;

class cart_products_automatically
{
	


	function __construct() {
		
		add_action( 'admin_menu', array($this,'cpa_add_admin_menu' ) );
		add_action( 'admin_init', array($this,'cpa_settings_init' ) );
		add_action( 'woocommerce_add_to_cart', array($this,'cpa_add_oneproduct_to_cart'), 10, 2 );
	    add_action( 'template_redirect', array($this,'cpa_add_freeproduct_to_cart' ) );
	    add_action( 'template_redirect', array($this,'remove_product_from_cart' ) );
	    add_action( 'template_redirect', array($this,'cpa_add_visitproduct_to_cart' ) );	
		add_action( 'plugins_loaded', 'load_plugin_textdomain' );

	}

	/**
	 *This function will load the text domain
	 *
	 */
	
	function load_plugin_textdomain() {
		
		load_plugin_textdomain( 'cart-products-automatically', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
	}
	
	/**
     *This function will add menu in admin panel
     *
	 */

	function cpa_add_admin_menu() { 

		add_menu_page( 'Cart Products Automatically', 'Cart Products Automatically', 'manage_options', 'cpa_cart_products_automatically', array($this,'cpa_options_page' ) );

	}

	/**
     *This function will create the menu page for admin and 
     *will register our settings in the wordpress database
     *
     */

	function cpa_settings_init() { 

		register_setting( 'pluginPage', 'cpa_oneprd' );
		register_setting( 'pluginPage', 'cpa_freeprd' );
		register_setting( 'pluginPage', 'cpa_checkfreeprd' );
		register_setting( 'pluginPage', 'cpa_removefreeprd' );
		register_setting( 'pluginPage', 'cpa_removetotalfreeprd' );
		register_setting( 'pluginPage', 'cpa_enteramt' );
		register_setting( 'pluginPage', 'cpa_totalcart' );
		register_setting( 'pluginPage', 'cpa_checktotalcart' );
		register_setting( 'pluginPage', 'cpa_visit' );
		register_setting( 'pluginPage', 'cpa_checkvisit' );

		/**
		 *This will display the header of the section(add to cart action)
		 *
		 */

		add_settings_section(
			'cpa_add_to_cart_action', 
			__( 'Add to Cart Action', 'cart-products-automatically' ), 
			array($this,'cpa_settings_add_to_cart_action'), 
			'pluginPage'
		);

		/**
		 *This setting field is for enabling add to cart function  
		 *
		 */

		add_settings_field( 
			'cpa_checkfreeprd', 
			__( 'Enable', 'cart-products-automatically' ), 
			array($this,'cpa_checkfreeprd_render'), 
			'pluginPage', 
			'cpa_add_to_cart_action' 
		);

		/**
		 *This setting field is to enter multiple category id's on which function will work
		 *
		 */

		add_settings_field( 
			'cpa_oneprd', 
			__( 'Multiple Category Id\'s', 'cart-products-automatically' ), 
			array($this,'cpa_oneprd_render'), 
			'pluginPage', 
			'cpa_add_to_cart_action' 
		);

		/**
		 *This setting field is to enter multiple product id's to be added automatically when add to cart function works
		 *
		 */

		add_settings_field( 
			'cpa_freeprd', 
			__( 'Multple Product Id\'s', 'cart-products-automatically' ), 
			array($this,'cpa_freeprd_render' ),
			'pluginPage', 
			'cpa_add_to_cart_action' 
		);

		/**
		 *This setting field is for enabling not to add the free product again once its removed 
		 *
		 */

		add_settings_field( 
			'cpa_removefreeprd', 
			__( 'Enable', 'cart-products-automatically' ), 
			array($this,'cpa_removefreeprd_render'), 
			'pluginPage', 
			'cpa_add_to_cart_action' 
		);

		/**
		 *This will display the header of the section(cart total)
		 *
		 */

		add_settings_section(
			'cpa_cart_total', 
			__( 'Cart Total', 'cart-products-automatically' ), 
			array($this,'cpa_settings_cart_total'), 
			'pluginPage'
		);

		/**
		 *This setting field is for enabling cart total function  
		 *
		 */

		add_settings_field( 
			'cpa_checktotalcart', 
			__( 'Enable', 'cart-products-automatically' ), 
			array($this,'cpa_checktotalcart_render'), 
			'pluginPage', 
			'cpa_cart_total' 
		);

		/**
		 *This setting field is to enter amount of cart total on exceeds of which cart total function will work .
		 *
		 */

		add_settings_field( 
			'cpa_enteramt', 
			__( 'Amount', 'cart-products-automatically' ), 
			array($this,'cpa_enteramt_render'), 
			'pluginPage', 
			'cpa_cart_total' 
		);

	    /**
		 *This setting field is to enter product id to be added automatically when cart total function works
		 *
		 */

		add_settings_field( 
			'cpa_totalcart', 
			__( 'Product id', 'cart-products-automatically' ), 
			array($this,'cpa_totalcart_render'), 
			'pluginPage', 
			'cpa_cart_total' 
		);

		/**
		 *This setting field is for enabling not to  the free product again once its removed 
		 *
		 */

		add_settings_field( 
			'cpa_removetotalfreeprd', 
			__( 'Enable', 'cart-products-automatically' ), 
			array($this,'cpa_removetotalfreeprd_render'), 
			'pluginPage', 
			'cpa_cart_total' 
		);

		/**
		 *This will display the header of the section(website visit)
		 *
		 */
		
		add_settings_section(
			'cpa_website_visit', 
			__( 'Website Visit', 'cart-products-automatically' ), 
			array($this,'cpa_settings_website_visit'), 
			'pluginPage'
		);

		/**
		 *This setting field is for enabling website visit function  
		 *
		 */

		add_settings_field( 
			'cpa_checkvisit', 
			__( 'Enable', 'cart-products-automatically' ), 
			array($this,'cpa_checkvisit_render'), 
			'pluginPage', 
			'cpa_website_visit' 
		);

		/**
		 *This setting field is to enter product id to be added automatically when website visit function works
		 *
		 */

		add_settings_field( 
			'cpa_visit', 
			__( 'Product id', 'cart-products-automatically' ), 
			array($this,'cpa_visit_render'), 
			'pluginPage', 
			'cpa_website_visit' 
		);

	}

	/**
	 *This function will create the checkbox to enable add to cart function
	 *
	 */

	function cpa_checkfreeprd_render() { 

		$cpa_checkfreeprd=get_option('cpa_checkfreeprd');

		?>
		<input type='checkbox' name='cpa_checkfreeprd' <?php checked(1, $cpa_checkfreeprd , true); ?> value='1'> <?php _e('Enables add to cart action function.','cart-products-automatically');?>
		<?php

	}

	/**
	 *This function will create the text box for category id in add to cart function 
	 *
	 */

	function cpa_oneprd_render() { 

		$cpa_oneprd=get_option('cpa_oneprd');

		if(!$cpa_oneprd){
			$cpa_oneprd="";
		}


		?>
		<input type='text' name='cpa_oneprd' value='<?php echo $cpa_oneprd; ?>'>
		</br><p> <?php _e('Enter category id on which function will work.','cart-products-automatically');?></p>
		<?php

	}

	/**
	 *This function will create the text box for product id in add to cart function 
	 *
	 */

	function cpa_freeprd_render() { 

		$cpa_freeprd=get_option('cpa_freeprd');
		
		if(!$cpa_freeprd){
			$cpa_freeprd="";
		}

		?>
		<input type='text' name='cpa_freeprd' value='<?php echo $cpa_freeprd; ?>'>
		</br><p> <?php _e('Enter id of a product which will be added to the cart when product of particular category is added to the cart.','cart-products-automatically');?> </p>
		<?php

	}

	/**
	 *This function will create the checkbox to enable not to add free product once it is removed
	 *
	 */

	function cpa_removefreeprd_render() { 

		$cpa_removefreeprd=get_option('cpa_removefreeprd');

		?>
		<input type='checkbox' name='cpa_removefreeprd' <?php checked(1, $cpa_removefreeprd , true); ?> value='1'> <?php _e('Enabling this will not add the free product again to the cart once it is removed by the user.','cart-products-automatically');?>
		<?php

	}

	/**
	 *This function will display under the header of add to cart section
	 *
	 */ 

	function cpa_settings_add_to_cart_action() { 

		echo __( 'When product of multiple category\'s is added to cart, then also add product with multiple\'s id to the cart.', 'cart-products-automatically' );

	}

	/**
	 *This function will create the checkbox to enable cart total function
	 *
	 */
	
	function cpa_checktotalcart_render() { 

		$cpa_checktotalcart=get_option('cpa_checktotalcart');

		?>
		<input type='checkbox' name='cpa_checktotalcart' <?php checked(1, $cpa_checktotalcart , true); ?> value='1'> <?php _e('Enables cart total function.','cart-products-automatically');?>
		<?php

	}

	/**
	 *This function will create the text box to enter amount in  cart total function 
	 *
	 */

	function cpa_enteramt_render() { 
		
		$cpa_enteramt=get_option('cpa_enteramt');

		if(!$cpa_enteramt){
			$cpa_enteramt="";
		}

		?>
		<input type='text' name='cpa_enteramt' value='<?php echo $cpa_enteramt; ?>'>
		</br><p> <?php _e('Enter amount of cart total on exceeds of which this function will work .','cart-products-automatically');?> </p>
		<?php

	}

	/**
	 *This function will create the text box for product id in cart total function 
	 *
	 */

	function cpa_totalcart_render() { 

		$cpa_totalcart=get_option('cpa_totalcart');

		if(!$cpa_totalcart){
			$cpa_totalcart="";
		}

		?>
		<input type='text' name='cpa_totalcart' value='<?php echo $cpa_totalcart; ?>'>
		</br><p> <?php _e('Enter id of a product which will be added to the cart when cart total will exceeds by certain amount.','cart-products-automatically'); ?> </p>
		<?php

	}

	/**
	 *This function will create the checkbox to enable not to add free product once it is removed
	 *
	 */

	function cpa_removetotalfreeprd_render() { 

		$cpa_removetotalfreeprd=get_option('cpa_removetotalfreeprd');

		?>
		<input type='checkbox' name='cpa_removetotalfreeprd' <?php checked(1, $cpa_removetotalfreeprd , true); ?> value='1'> <?php _e('Enabling this will not add the free product again to the cart once it is removed by the user.','cart-products-automatically');?>
		<?php

	}


	/**
	 *This function will display under the header of cart total section
	 *
	 */ 

	function cpa_settings_cart_total() { 

		echo __( 'When total of cart exceeds by certain amount.', 'cart-products-automatically' );

	}

	/**
	 *This function will create the checkbox to enable website visit function
	 *
	 */

	function cpa_checkvisit_render() { 

		$cpa_checkvisit=get_option('cpa_checkvisit');

		if(!$cpa_checkvisit){
			$cpa_checkvisit="";
		}
		?>
		<input type='checkbox' name='cpa_checkvisit' <?php checked(1, $cpa_checkvisit , true); ?> value='1'> <?php _e('Enables add to cart action function.','cart-products-automatically');?>
		<?php

	}

	/**
	 *This function will create the text box for product id in website visit function 
	 *
	 */

	function cpa_visit_render() { 

		$cpa_visit=get_option('cpa_visit');

		if(!$cpa_visit){
			$cpa_visit="";
		}

		?>
		<input type='text' name='cpa_visit' value=' <?php echo $cpa_visit; ?>'>
		</br><p> <?php _e('Enter category id on which function will work.','cart-products-automatically'); ?></p>
		<?php

	}

	/**
	 *This function will display under the header of website visit section
	 *
	 */ 

	function cpa_settings_website_visit() { 

		echo __( 'Adding product to the cart when a customer visits your website.', 'cart-products-automatically' );

	}

	/**
	 *This function will display the main header of the page
	 *
	 */

	function cpa_options_page() { 

			?>
			<form action='options.php' method='post'>

				<h2> <?php _e('Automatically Add a Product Into Cart.','cart-products-automatically'); ?></h2>
				<p> <?php _e('This will automatically add a product to your WooCommerce cart in 3 different scenarios.','cart-products-automatically'); ?></p>
				</br>

				<?php
				settings_fields( 'pluginPage' );
				do_settings_sections( 'pluginPage' );
				submit_button();
				?>

			</form>
			<?php

	}

	/**
	 *This function is for adding the multiple product id to the cart if the
	  users adds the multiple category product to the cart.
	 *
	 */

 	function cpa_add_oneproduct_to_cart( $item_key, $product_id ) { 

	 	if(get_option('cpa_checkfreeprd') == 1) {
	 			 		
	 			$remove=true;
		 		$cpa_oneprd=get_option('cpa_oneprd');
				$cpa_freeprd=get_option('cpa_freeprd');
			 	$product_category_id 	= explode(",", $cpa_oneprd); //Category ids 
			 	$free_product_id = explode(",",$cpa_freeprd); 

			if(get_option('cpa_removefreeprd') == 1){

	 			if ( isset( WC()->session ) && !is_null( WC()->session->get( 'removed_cart_contents' ) ) && WC()->session->get( 'removed_cart_contents' ) != '' ) { // checking is any products is removed or not.
				
					$removed_cart_contents = WC()->session->get( 'removed_cart_contents' );
				
					foreach ( $removed_cart_contents as $key => $value ) {
						$list_of_removed_product[] = $value['product_id'];			
					}
				
					$list_of_removed_product = array_unique( $list_of_removed_product );
				
					foreach ( $free_product_id as $key => $free_product_ids ) {
								
						if ( in_array( $free_product_ids, $list_of_removed_product ) ){ // If free product found in 													list of removed product then do nothing.
							$remove=false;
							break; // do nothing if product is already removed by customer.
						}
					}
				}
			}

			if($remove){
							
				$product_cats_ids 		= wc_get_product_term_ids( $product_id, 'product_cat' ); // Getting assigned categories of product which is being added to cart
				$product_category_id_check = false;
				
				foreach ( $product_category_id as $key => $value ) {

					if ( in_array( $value, $product_cats_ids ) ) { // Checking if the specified category is being 													  matched or 	not.
						$product_category_id_check = true;
					}
				}
			    
			    if ( ! is_admin() && $product_category_id_check ) {
			        		
			        $free_product_ids = explode(",",$cpa_freeprd);  // Product Ids of the free products which will 													  get added to cart e.g Gift A & Gift B
			        	
				    foreach ( $free_product_ids as $pkey => $free_product_id ) {

				        $found = false;
					    //check if product already in cart
					        
					    if ( sizeof( WC()->cart->get_cart() ) > 0 ) {

					        foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
					                
					        	$_product = $values['data'];
					                
					        	if ( $_product->get_id() == $free_product_id )
					               	$found = true;
					        }
					        	// if product not found, add it
					        	if ( ! $found )
					           	WC()->cart->add_to_cart( $free_product_id );
					    }
						
						else {
					        // if no products in cart, add it
					        WC()->cart->add_to_cart( $free_product_id );
					    }
			        }                
		    	} 
	    	} 
	    }
	}

	/**
	 *This function is for adding the product with particular id when
	  the total of cart exceeds certain amount.
	 *
	 */
	
	function cpa_add_freeproduct_to_cart() {

		if(get_option('cpa_checktotalcart') == 1) {
				
				$remove=true;	
				global $woocommerce;
				$cart_total	=get_option('cpa_enteramt');
				$list_of_removed_product 	= array(); // In this we will add all the removed product ids
				$free_product_id = get_option('cpa_totalcart');

			if(get_option('cpa_removetotalfreeprd') == 1) {	

				if ( isset( WC()->session ) && !is_null( WC()->session->get( 'removed_cart_contents' ) ) && WC()->session->get( 'removed_cart_contents' ) != '' ) { // checking is any products is removed or not.
				
					$removed_cart_contents = WC()->session->get( 'removed_cart_contents' );
					
					foreach ( $removed_cart_contents as $key => $value ) {
						$list_of_removed_product[] = $value['product_id'];			
					}
					
					$list_of_removed_product = array_unique( $list_of_removed_product );
				
				}
					
					if ( in_array( $free_product_id, $list_of_removed_product ) ){ // If free product found in list of 																	removed product then do nothing.
						$remove=false;
						return; // do nothing if product is already removed by customer.
					}
			}
									
			if($remove){

			 	global $woocommerce;
								
				if ( $woocommerce->cart->total >= $cart_total ) {
					
					if ( ! is_admin() && !is_cart() && !is_checkout()  ) {
					
				       	$free_product_id = get_option('cpa_totalcart'); // Product Id of the free product which will 													get added to cart
				       	$found 		= false;
				      
				       	//check if product already in cart
				        if ( sizeof( WC()->cart->get_cart() ) > 0 ) {

					        foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
					            	    
					            $_product = $values['data'];
					                
					            if ( $_product->get_id() == $free_product_id )
					                $found = true;	                
					        }
					        // if product not found, add it
					        if ( ! $found ){
					            WC()->cart->add_to_cart( $free_product_id );
					        }
					    } 
					    
					    else{
					        // if no products in cart, add it
					        WC()->cart->add_to_cart( $free_product_id );
					    }        
					}
				}
				
				/**
				 *This else will remove the automatically added product from the cart when
				  the cart total is less than the certain amount added by the admin
				 *
				 */	
				
				else{
						
					if ( ! is_admin()  ) {
				    
				        $free_product_id = get_option('cpa_totalcart'); // Product Id of the free product which will 													get added to cart
					    $found = false;

					    //check if product already in cart
					    if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
					            
					        foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
					            	
					           	$_product = $values['data'];
					                
					            if ( $_product->get_id() != $free_product_id ){
					              	$found = false;
					            }
					            else{
					          	$found = true;
					          	break;
					            }
					        }
					        if($found){
				  	    		$free_pro_cart_id = WC()->cart->generate_cart_id( $free_product_id );
				          		unset( WC()->cart->cart_contents[ $free_pro_cart_id ]);
					       	}
					    }        
					}
				}
			}
		}
	}
	
	/**
	 *This function will automatically adds the product with particualar id
	  to the cart when customer visits our website.
	 *
	 */

	function cpa_add_visitproduct_to_cart() {
	   
		if(get_option('cpa_checkvisit') == 1) {

	    	if ( ! is_admin() && !is_cart() && !is_checkout() ) {
	        	
	        	$product_id =   get_option('cpa_visit'); // Product Id of the free product which will get added to cart
	        	$found 	= false;
	        	
	        	//check if product already in cart
	        	if ( sizeof( WC()->cart->get_cart() ) > 0 ) {

	            	foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
	                   	$_product = $values['data'];

	                	if ( $_product->get_id() == $product_id )
	                    	$found = true;
	            	}

	            	// if product not found, add it
	            	if ( ! $found )
	                   	WC()->cart->add_to_cart( $product_id );
	        	}
	        	
	        	else{
	         	   // if no products in cart, add it
	            	WC()->cart->add_to_cart( $product_id );
	        	}
	   		}	    
		}
	}

	/**
	 *This function will remove the automatically added product when the product of particular category is removed by the user. 
	 *
	 */

	function remove_product_from_cart() {
    
	    	$cpa_oneprd=get_option('cpa_oneprd');
			$cpa_freeprd=get_option('cpa_freeprd');

	    // Run only in the Cart or Checkout Page
	    if ( is_cart() || is_checkout() ) {
	    	
	    	$product_category_id 	=  explode(",", $cpa_oneprd);// ID of category       
	        $prod_to_remove     	= explode(",",$cpa_freeprd); // Product ID of Free Product
	        $cart_contains_category = false; // Default set to false : This means cart doesn't contains any product of perticular category
	        $free_pro_cart_id 		= "";

	        foreach ( WC()->cart->cart_contents as $prod_in_cart ) {
	          
	            // Get the Variation or Product ID
	            $prod_id = ( isset( $prod_in_cart['variation_id'] ) && $prod_in_cart['variation_id'] != 0 ) ? $prod_in_cart['variation_id'] : $prod_in_cart['product_id'];
	            $product_cats_ids = wc_get_product_term_ids( $prod_id, 'product_cat' );

	        	foreach ( $product_category_id as $key => $value ) {
		
	     	       if ( in_array( $value, $product_cats_ids ) ){
	        	    	$cart_contains_category = true; // cart has the product of particular category.            	
	            		break;
	            	}
	        	}
	     	}
	        
	        foreach ( $prod_to_remove as $key => $value1 ) {
	        	
	        	if ( !$cart_contains_category ) { // remove free product if cart doesn't contain product of perticular 										category
	        		$free_pro_cart_id = WC()->cart->generate_cart_id( $value1 );
	            	// Remove it from the cart by un-setting it
	            	unset( WC()->cart->cart_contents[ $free_pro_cart_id ] );             
    	    	}
    		}
    	}
	}


}

new cart_products_automatically();

?>