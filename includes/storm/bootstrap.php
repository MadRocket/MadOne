<?php 
	                function Model_Module( $params = null )
	                {
	                    return $params ? new Model_Module( $params ) : new Model_Module();
	                }

	                function Model_Modules( $params = null )
	                {
	                    $qs = new Storm_Queryset( 'Model_Module' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function Model_Pagetype( $params = null )
	                {
	                    return $params ? new Model_Pagetype( $params ) : new Model_Pagetype();
	                }

	                function Model_Pagetypes( $params = null )
	                {
	                    $qs = new Storm_Queryset( 'Model_Pagetype' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function Model_Page( $params = null )
	                {
	                    return $params ? new Model_Page( $params ) : new Model_Page();
	                }

	                function Model_Pages( $params = null )
	                {
	                    $qs = new Storm_Queryset_Tree( 'Model_Page' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function Model_Textblock( $params = null )
	                {
	                    return $params ? new Model_Textblock( $params ) : new Model_Textblock();
	                }

	                function Model_Textblocks( $params = null )
	                {
	                    $qs = new Storm_Queryset( 'Model_Textblock' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function Model_User( $params = null )
	                {
	                    return $params ? new Model_User( $params ) : new Model_User();
	                }

	                function Model_Users( $params = null )
	                {
	                    $qs = new Storm_Queryset( 'Model_User' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function Model_News( $params = null )
	                {
	                    return $params ? new Model_News( $params ) : new Model_News();
	                }

	                function Model_Newslist( $params = null )
	                {
	                    $qs = new Storm_Queryset( 'Model_News' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function Model_Galleryimage( $params = null )
	                {
	                    return $params ? new Model_Galleryimage( $params ) : new Model_Galleryimage();
	                }

	                function Model_Galleryimages( $params = null )
	                {
	                    $qs = new Storm_Queryset( 'Model_Galleryimage' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function Model_Showcaseitem( $params = null )
	                {
	                    return $params ? new Model_Showcaseitem( $params ) : new Model_Showcaseitem();
	                }

	                function Model_Showcaseitems( $params = null )
	                {
	                    $qs = new Storm_Queryset( 'Model_Showcaseitem' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function Model_Feedbackmessage( $params = null )
	                {
	                    return $params ? new Model_Feedbackmessage( $params ) : new Model_Feedbackmessage();
	                }

	                function Model_Feedbackmessages( $params = null )
	                {
	                    $qs = new Storm_Queryset( 'Model_Feedbackmessage' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function Model_Tempimage( $params = null )
	                {
	                    return $params ? new Model_Tempimage( $params ) : new Model_Tempimage();
	                }

	                function Model_Tempimages( $params = null )
	                {
	                    $qs = new Storm_Queryset( 'Model_Tempimage' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function Model_Tempfile( $params = null )
	                {
	                    return $params ? new Model_Tempfile( $params ) : new Model_Tempfile();
	                }

	                function Model_Tempfiles( $params = null )
	                {
	                    $qs = new Storm_Queryset( 'Model_Tempfile' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function Model_Showcaseimage( $params = null )
	                {
	                    return $params ? new Model_Showcaseimage( $params ) : new Model_Showcaseimage();
	                }

	                function Model_Showcaseimages( $params = null )
	                {
	                    $qs = new Storm_Queryset( 'Model_Showcaseimage' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function Model_Subscriptionrecipient( $params = null )
	                {
	                    return $params ? new Model_Subscriptionrecipient( $params ) : new Model_Subscriptionrecipient();
	                }

	                function Model_Subscriptionrecipients( $params = null )
	                {
	                    $qs = new Storm_Queryset( 'Model_Subscriptionrecipient' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                