<?php
global $thecartpress;
$disable_ecommerce = $thecartpress->get_setting( 'disable_ecommerce', false );
if ( ! $disable_ecommerce ) {
	require_once( 'ShoppingCartSummaryWidget.class.php' );
	require_once( 'ShoppingCartWidget.class.php' );
	require_once( 'LastVisitedWidget.class.php' );
	require_once( 'RelatedListWidget.class.php' );
	require_once( 'CheckoutWidget.class.php' );
	register_widget( 'ShoppingCartSummaryWidget' );
	register_widget( 'ShoppingCartWidget' );
	register_widget( 'LastVisitedWidget' );
	register_widget( 'RelatedListWidget' );
	register_widget( 'CheckoutWidget' );//TODO At this moment, only for testing purpouse
}
require_once( 'CustomPostTypeListWidget.class.php' );
require_once( 'TaxonomyCloudsPostTypeWidget.class.php' );
require_once( TCP_WIDGETS_FOLDER . 'TaxonomyTreesPostTypeWidget.class.php' );
require_once( 'SortPanelWidget.class.php' );
require_once( 'CommentsCustomPostTypeWidget.class.php' );
require_once( 'BrothersListWidget.class.php' );
require_once( 'ArchivesWidget.class.php' );
require_once( 'AttributesListWidget.class.php' );
register_widget( 'CustomPostTypeListWidget' );
register_widget( 'TaxonomyCloudsPostTypeWidget' );
register_widget( 'TaxonomyTreesPostTypeWidget' );
register_widget( 'SortPanelWidget' );
register_widget( 'CommentsCustomPostTypeWidget' );
register_widget( 'BrothersListWidget' );
register_widget( 'TCPArchivesWidget' );
register_widget( 'AttributesListWidget' );
//register_widget( 'TCPCalendar' );
?>