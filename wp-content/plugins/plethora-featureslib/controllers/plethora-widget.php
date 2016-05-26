<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2015

File Description: Controller class for widgets

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Widget') ) {
 
    /**
     * @package Plethora Controllers
     */
    class Plethora_Widget {

        public static $controller_title             = 'Widgets Manager';                        // CONTROLLER TITLE
        public static $controller_description       = 'Activate/deactivate any Plethora widget available. Notice that on deactivation, all dependent features will be deactivated automatically.';
        public static $controller_dynamic_construct = false;                       // DYNAMIC CLASS CONSTRUCTION 
        public static $controller_dynamic_method    = false;                          // INVOKE ANY METHOD AFTER DYNAMIC CONSTRUCTION? ( method || false )
        public static $dynamic_features_loading     = true;                             // LOAD FEATURES DYNAMICALLY ( always true, false if stated so in controller variables )

        /**
        * Enable Media Manager
        * @return string
        */
        public static function enableMedia(){

            global $pagenow;

            if ( $pagenow !== 'widgets.php' /* && $pagenow!== 'customize.php' */ ) return;

            add_action( 'admin_enqueue_scripts', create_function( "", "wp_enqueue_script('upload_media_widget', PLE_CORE_JS_URI . '/upload-media.js', array('jquery'), false, true);") );  

            wp_enqueue_media();

        }

        /**
        * Return form default values ( use in widget_class::form() method )
        * @return string
        */
        public static function get_form_defaults( $params ) {

            $defaults = array();
            foreach ( $params as $key => $param ) {

                $defaults[$param['param_name']] = isset( $param['value'] ) && ! is_array($param['value']) ? $param['value'] : ''; 
            }

            return $defaults;
        }

        /**
        * Return widget values for template or shortcode outputs ( use in widget_class::widget() method )
        * @return string
        */
        public static function get_widget_atts( $params, $args, $instance ) {

            extract( $args );   // EXTRACT USER INPUT

            // PACK DEFAULT VALUES
            $widget_atts = array (
                                'widget_id'     => $widget_id,  
                                'before_widget' => $before_widget,  
                                'after_widget'  => $after_widget,  
                                'before_title'  => $before_title,  
                                'after_title'   => $after_title
                                );
            
            // PACK ADDITIONAL VALUES 
            $widget_add_atts = array();
            foreach ( $params as $key => $param ) {

                if ( isset( $param['is_widget_title'] ) &&  $param['is_widget_title'] ) { // title should be filtered
                    
                    $widget_add_atts[$param['param_name']] = apply_filters('widget_title', $instance[$param['param_name']] ); 
                } else {

                    $widget_add_atts[$param['param_name']] = $instance[$param['param_name']]; 
                }
            }

            // MERGE AND GO! 
            $widget_atts = array_merge( $widget_atts, $widget_add_atts );
            return $widget_atts;
        }
       
        /**
        * Returns a field
        * @return string
        */
        public static function get_field( $args ) {

            $output = '';

            if ( isset( $args['type'] ) ) {

            $output .= '<p>';
            $output .= '<label for="' . self::get_field_id( $args['param_name'], $args['obj'] ) . '"><strong>' . $args['heading'] . '</strong></label>';
                switch ( $args['type'] ) {
                    case 'textfield':
                    default:
                        $output .= self::field_textfield( $args );
                        break;
                    
                    case 'textarea':
                        $output .= self::field_textarea( $args );
                        break;
                    
                    case 'dropdown':
                        $output .= self::field_dropdown( $args );
                        break;

                    case 'attach_image':
                        $output .= self::field_attach_image( $args );
                        break;

                    case 'iconpicker':
                        $output .= self::field_iconpicker( $args );
                        break;
                }
            }
            $output .= '</p>';

            return $output;
        }

        /**
        * Returns a text option field
        * @return string
        */
        public static function field_textfield( $args ) {

            $obj = $args['obj'];
            $instance = $args['instance'];
            $output =  '<input type="text" class="widefat" id="' . self::get_field_id( $args['param_name'], $args['obj'] ) . '" name="' . self::get_field_name( $args['param_name'], $args['obj'] ) .'" value="' . esc_attr( $instance[$args['param_name']] ) . '" />';
            return $output;
        }

        /**
        * Returns a text option field
        * @return string
        */
        public static function field_textarea( $args ) {

            $obj = $args['obj'];
            $instance = $args['instance'];
            $output =  '<textarea rows="5" class="widefat" id="' . self::get_field_id( $args['param_name'], $args['obj'] ) . '" name="' . self::get_field_name( $args['param_name'], $args['obj'] ) .'">' . $instance[$args['param_name']]  . '</textarea>';
            return $output;
        }

        /**
        * Returns a select option field
        * @return string
        */
        public static function field_dropdown( $args ) {

            $obj = $args['obj'];
            $instance = $args['instance'];
            $output = '<select class="widefat" id="' . self::get_field_id( $args['param_name'], $args['obj'] ) . '" name="' . self::get_field_name( $args['param_name'], $args['obj'] ) .'" >';
            foreach ( $args['value'] as $opt_title => $opt_val ) {

                $select = $instance[$args['param_name']] === $opt_val ? ' selected' : '';
                $output .= '<option value="'. esc_attr( $opt_val ).'"'.$select.'>'. $opt_title.'</option>';
            }
            $output .= '</select>';
            return $output;
        }

        /**
        * Returns a select option field
        * @return string
        */
        public static function field_iconpicker( $args ) {

            $obj = $args['obj'];
            $instance = $args['instance'];
            $libraries = isset( $args['settings']['type'] ) ? array( $args['settings']['type'] ) : array();
            $libraries = Plethora_Module_Icons::get_options_array( array( 'use_in' => 'vc', 'library' => $libraries ) );
            $output = '<select class="widefat" id="' . self::get_field_id( $args['param_name'], $args['obj'] ) . '" name="' . self::get_field_name( $args['param_name'], $args['obj'] ) .'" >';
            $output .= '<option value="">'. esc_html__('No icon selected', 'plethora-framework' ).'</option>';
            foreach ( $libraries as $key => $icons ) {
                asort($icons, SORT_STRING);
                foreach ( $icons as $icon_val => $icon_title ) {

                    $select = $instance[$args['param_name']] === $icon_val ? ' selected' : '';
                    $output .= '<option value="'. esc_attr( $icon_val ).'"'.$select.'>'. $icon_title.'</option>';
                }
            }
            $output .= '</select>';
            return $output;
        }

        /**
        * Returns a select option field
        * @return string
        */
        public static function field_attach_image( $args ) {

            $obj = $args['obj'];
            $instance = $args['instance'];
            $output = '';

            $output .= '<p class="media-manager">';
            $output .= '  <img class="'. esc_attr( $obj->id ).'_thumbnail" src="'.  esc_url( $instance[$args['param_name']] ) .'" style="margin:0;padding:0;max-width:100px;float:left;display:inline-block" />';
            $output .= '  <input type="text" class="widefat '.esc_attr( $obj->id ).'_url" name="'.  esc_attr( self::get_field_name( $args['param_name'], $args['obj'] ) ) .'" id="'. esc_attr( self::get_field_id( $args['param_name'], $args['obj'] ) ) .'" value="'. esc_url( $instance[$args['param_name']] ) .'">';
            $output .= '  <input type="button" value="'. esc_html__('Upload Image', 'plethora-framework') .'" class="button custom_media_upload" id="'. esc_attr( $obj->id ) .'"/>';
            $output .= '</p>';

            return $output;
        }

        /**
         * Returns output content in shortcode mode...used for widgets that act as a shortcode replica
         * @param $params $instance
         * @return string
         */
        public static function get_shortcode_output( $shortcode_tag, $widget_atts ) {

            $shortcode = '['.$shortcode_tag.' ';
            foreach ( $widget_atts as $att_key => $att_val ) {
               
                if ( !in_array($att_key, array('widget_id', 'before_widget', 'after_widget', 'before_title', 'after_title', 'id_base') ) ) { // exclude general widget args

                    if ( $att_key === 'content') { // get content attr separately

                        $content = $att_val;

                    } else {

                        $shortcode .= ' '. $att_key .'="'. $att_val .'"';
                    }
                }
            }
            $shortcode .= ']';
            // If $content has contents, then this must be an enclosed shortcode
            if ( isset($content) ) {

                $content = !empty($content) ? wpautop( $content, true ) : '';
                $shortcode .= !empty($content) ? do_shortcode( $content ) : '';
                $shortcode .= '[/'.$shortcode_tag.']';
            }

            $output  = '<div class="'.$widget_atts['id_base'].'">'; // PLENOTE: we should prototype each widget class according to id_base
            $output .= do_shortcode( $shortcode );
            $output .= '</div>';
            return $output;

        }

        public static function get_templatepart_output( $widget_atts, $file ) {

            if ( !empty( $widget_atts['content'] ) ) {

                // $widget_atts['content'] = wpautop( $widget_atts['content'], true );
                $widget_atts['content'] = do_shortcode( $widget_atts['content'] );
            }

            $output = Plethora_WP::renderMustache( array( "data" => $widget_atts, "file" => $file ) );
            return $output;
        }

        /**
         * Constructs name attributes for use in form() fields
         * @param string $field_name Field name
         * @return string Name attribute for $field_name
         */
        public static function get_field_name( $field_name, $obj ) {
            return 'widget-' . $obj->id_base . '[' . $obj->number . '][' . $field_name . ']';
        }

        /**
         * Constructs id attributes for use in {@see WP_Widget::form()} fields.
         * @param string $field_name Field name.
         * @return string ID attribute for `$field_name`.
         */
        public static function get_field_id( $field_name, $obj ) {
            return 'widget-' . $obj->id_base . '-' . $obj->number . '-' . $field_name;
        }
    }
 }