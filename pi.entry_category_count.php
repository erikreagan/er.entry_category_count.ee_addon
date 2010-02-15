<?php

/**
 * Entry Category Count
 * 
 * This file must be placed in the
 * system/plugins/ folder in your ExpressionEngine installation.
 *
 * @package EntryCategoryCount
 * @version 1.0
 * @author Erik Reagan http://erikreagan.com
 * @copyright Copyright (c) 2010 Erik Reagan
 * @see http://erikreagan.com/projects/entry-category-count/
 */

$plugin_info       = array(
   'pi_name'        => 'Entry Category Count',
   'pi_version'     => '1.0.0',
   'pi_author'      => 'Erik Reagan',
   'pi_author_url'  => 'http://erikreagan.com',
   'pi_description' => 'Returns the number of categories assigned to any entry',
   'pi_usage'       => Entry_category_count::usage()
   );

class Entry_category_count
{

   var $return_data  = "";

   function Entry_category_count()
   {
      global $DB, $TMPL;
      
      $entry_id = ($TMPL->fetch_param('entry_id') != '') ? $TMPL->fetch_param('entry_id') : '0';
      $TMPL->log_item('Entry Category Count Plugin: Entry: '.$entry_id);
      
      $show_group = ($TMPL->fetch_param('show_group') != '') ? $TMPL->fetch_param('show_group') : FALSE ;
      $TMPL->log_item('Entry Category Count Plugin: Show Group: '.$show_group);
      
      $show = ($TMPL->fetch_param('show') != '') ? $TMPL->fetch_param('show') : FALSE ;
      $TMPL->log_item('Entry Category Count Plugin: Show Categories: '.$show);
      
      // Build our query now
      $query = "SELECT COUNT(*) FROM exp_category_posts WHERE entry_id = '".$DB->escape_str($entry_id)."'";
      
      if($show)
      {
         $query .= " AND cat_id ";
         
         $cat_ids = str_replace('|',',',$show);
         if(strpos($show,'not') !== FALSE)
         {
            $cat_ids = str_replace('not ','',$cat_ids);
            $query .= "NOT ";
         }
         
         $query .= "IN ($cat_ids)";
      }
      
      if($show_group)
      {
         $query .= " AND cat_id ";
         
         $group_ids = str_replace('|',',',$show_group);
         if(strpos($show_group,'not') !== FALSE)
         {
            $group_ids = str_replace('not ','',$group_ids);
            $query .= "NOT ";
         }
         
         $query .= "IN (SELECT cat_id FROM exp_categories WHERE group_id IN ($group_ids))";
      }
      $TMPL->log_item('Entry Category Count Plugin: Query: '.$query);
      
      $category_count = $DB->query($query);
      $this->return_data = $category_count->result[0]['COUNT(*)'];
   }

   /**
    * Plugin Usage
    */

   // This function describes how the plugin is used.
   //  Make sure and use output buffering

   function usage()
   {
      ob_start(); 
?>
This very simple plugin just returns the total categories assigned to the entry based on the entry_id parameter. Here are a few sample uses


Total Assigned Categories: {exp:entry_category_count entry_id="{entry_id}"}


{if {exp:entry_category_count entry_id="{entry_id}"} > 1}
  There are multiple categories assigned to this entry
{/if}


{if {exp:entry_category_count entry_id="{entry_id}"} == 0}
  There are no categories assigned to this entry
{/if}

============

You can hardcode in an entry ID if you like
{if {exp:entry_category_count entry_id="124"} > 1}
  Your very awesome code here
{/if}


<?php
      $buffer         = ob_get_contents();

      ob_end_clean(); 

      return $buffer;
   }
   // END

}


/* End of file pi.entry_category_count.php */
/* Location: ./system/plugins/pi.er_entry_category_count.php */