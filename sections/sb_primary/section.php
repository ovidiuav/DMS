<?php
/*
	Section: Primary Sidebar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: The main widgetized sidebar.
	Class Name: PrimarySidebar
	Tags: internal
	Workswith: sidebar1, sidebar2, sidebar_wrap
*/

class PrimarySidebar extends PageLinesSection {

   function section_persistent() { 
		$setup = pagelines_standard_sidebar($this->name, $this->settings['description']);
		register_sidebar($setup);
	}

   function section_template() { 
	 	 pagelines_draw_sidebar($this->id, $this->name, 'includes/widgets.default');
	}

}

/*
	End of section class
*/