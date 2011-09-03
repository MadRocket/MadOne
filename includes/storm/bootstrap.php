<?php 
	                function MadoneModule( $params = null )
	                {
	                    return $params ? new MadoneModule( $params ) : new MadoneModule();
	                }

	                function MadoneModules( $params = null )
	                {
	                    $qs = new StormQuerySet( 'MadoneModule' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadonePageType( $params = null )
	                {
	                    return $params ? new MadonePageType( $params ) : new MadonePageType();
	                }

	                function MadonePageTypes( $params = null )
	                {
	                    $qs = new StormQuerySet( 'MadonePageType' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadonePage( $params = null )
	                {
	                    return $params ? new MadonePage( $params ) : new MadonePage();
	                }

	                function MadonePages( $params = null )
	                {
	                    $qs = new StormKiQuerySet( 'MadonePage' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadonePageContent( $params = null )
	                {
	                    return $params ? new MadonePageContent( $params ) : new MadonePageContent();
	                }

	                function MadonePageContents( $params = null )
	                {
	                    $qs = new StormQuerySet( 'MadonePageContent' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadoneTextBlock( $params = null )
	                {
	                    return $params ? new MadoneTextBlock( $params ) : new MadoneTextBlock();
	                }

	                function MadoneTextBlocks( $params = null )
	                {
	                    $qs = new StormQuerySet( 'MadoneTextBlock' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadoneUser( $params = null )
	                {
	                    return $params ? new MadoneUser( $params ) : new MadoneUser();
	                }

	                function MadoneUsers( $params = null )
	                {
	                    $qs = new StormQuerySet( 'MadoneUser' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadoneNews( $params = null )
	                {
	                    return $params ? new MadoneNews( $params ) : new MadoneNews();
	                }

	                function MadoneNewsList( $params = null )
	                {
	                    $qs = new StormQuerySet( 'MadoneNews' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadoneGallerySection( $params = null )
	                {
	                    return $params ? new MadoneGallerySection( $params ) : new MadoneGallerySection();
	                }

	                function MadoneGallerySections( $params = null )
	                {
	                    $qs = new StormKiQuerySet( 'MadoneGallerySection' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadoneGalleryImage( $params = null )
	                {
	                    return $params ? new MadoneGalleryImage( $params ) : new MadoneGalleryImage();
	                }

	                function MadoneGalleryImages( $params = null )
	                {
	                    $qs = new StormQuerySet( 'MadoneGalleryImage' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadoneShowcaseSection( $params = null )
	                {
	                    return $params ? new MadoneShowcaseSection( $params ) : new MadoneShowcaseSection();
	                }

	                function MadoneShowcaseSections( $params = null )
	                {
	                    $qs = new StormKiQuerySet( 'MadoneShowcaseSection' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadoneShowcaseItem( $params = null )
	                {
	                    return $params ? new MadoneShowcaseItem( $params ) : new MadoneShowcaseItem();
	                }

	                function MadoneShowcaseItems( $params = null )
	                {
	                    $qs = new StormQuerySet( 'MadoneShowcaseItem' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadoneFeedbackMessage( $params = null )
	                {
	                    return $params ? new MadoneFeedbackMessage( $params ) : new MadoneFeedbackMessage();
	                }

	                function MadoneFeedbackMessages( $params = null )
	                {
	                    $qs = new StormQuerySet( 'MadoneFeedbackMessage' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadoneTempImage( $params = null )
	                {
	                    return $params ? new MadoneTempImage( $params ) : new MadoneTempImage();
	                }

	                function MadoneTempImages( $params = null )
	                {
	                    $qs = new StormQuerySet( 'MadoneTempImage' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadoneTempFile( $params = null )
	                {
	                    return $params ? new MadoneTempFile( $params ) : new MadoneTempFile();
	                }

	                function MadoneTempFiles( $params = null )
	                {
	                    $qs = new StormQuerySet( 'MadoneTempFile' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadoneShowcaseImage( $params = null )
	                {
	                    return $params ? new MadoneShowcaseImage( $params ) : new MadoneShowcaseImage();
	                }

	                function MadoneShowcaseImages( $params = null )
	                {
	                    $qs = new StormQuerySet( 'MadoneShowcaseImage' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadoneShowcaseMovie( $params = null )
	                {
	                    return $params ? new MadoneShowcaseMovie( $params ) : new MadoneShowcaseMovie();
	                }

	                function MadoneShowcaseMovies( $params = null )
	                {
	                    $qs = new StormQuerySet( 'MadoneShowcaseMovie' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadoneSubscriptionRecipient( $params = null )
	                {
	                    return $params ? new MadoneSubscriptionRecipient( $params ) : new MadoneSubscriptionRecipient();
	                }

	                function MadoneSubscriptionRecipients( $params = null )
	                {
	                    $qs = new StormQuerySet( 'MadoneSubscriptionRecipient' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                