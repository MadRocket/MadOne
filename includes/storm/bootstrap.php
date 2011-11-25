<?php 
	                function MadoneModule( $params = null )
	                {
	                    return $params ? new MadoneModule( $params ) : new MadoneModule();
	                }

	                function MadoneModules( $params = null )
	                {
	                    $qs = new Storm_Queryset( 'MadoneModule' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadonePageType( $params = null )
	                {
	                    return $params ? new MadonePageType( $params ) : new MadonePageType();
	                }

	                function MadonePageTypes( $params = null )
	                {
	                    $qs = new Storm_Queryset( 'MadonePageType' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadonePage( $params = null )
	                {
	                    return $params ? new MadonePage( $params ) : new MadonePage();
	                }

	                function MadonePages( $params = null )
	                {
	                    $qs = new Storm_Queryset_Tree( 'MadonePage' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadoneTextBlock( $params = null )
	                {
	                    return $params ? new MadoneTextBlock( $params ) : new MadoneTextBlock();
	                }

	                function MadoneTextBlocks( $params = null )
	                {
	                    $qs = new Storm_Queryset( 'MadoneTextBlock' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadoneUser( $params = null )
	                {
	                    return $params ? new MadoneUser( $params ) : new MadoneUser();
	                }

	                function MadoneUsers( $params = null )
	                {
	                    $qs = new Storm_Queryset( 'MadoneUser' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadoneNews( $params = null )
	                {
	                    return $params ? new MadoneNews( $params ) : new MadoneNews();
	                }

	                function MadoneNewsList( $params = null )
	                {
	                    $qs = new Storm_Queryset( 'MadoneNews' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadoneGallerySection( $params = null )
	                {
	                    return $params ? new MadoneGallerySection( $params ) : new MadoneGallerySection();
	                }

	                function MadoneGallerySections( $params = null )
	                {
	                    $qs = new Storm_Queryset_Tree( 'MadoneGallerySection' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadoneGalleryImage( $params = null )
	                {
	                    return $params ? new MadoneGalleryImage( $params ) : new MadoneGalleryImage();
	                }

	                function MadoneGalleryImages( $params = null )
	                {
	                    $qs = new Storm_Queryset( 'MadoneGalleryImage' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadoneShowcaseSection( $params = null )
	                {
	                    return $params ? new MadoneShowcaseSection( $params ) : new MadoneShowcaseSection();
	                }

	                function MadoneShowcaseSections( $params = null )
	                {
	                    $qs = new Storm_Queryset_Tree( 'MadoneShowcaseSection' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadoneShowcaseItem( $params = null )
	                {
	                    return $params ? new MadoneShowcaseItem( $params ) : new MadoneShowcaseItem();
	                }

	                function MadoneShowcaseItems( $params = null )
	                {
	                    $qs = new Storm_Queryset( 'MadoneShowcaseItem' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadoneFeedbackMessage( $params = null )
	                {
	                    return $params ? new MadoneFeedbackMessage( $params ) : new MadoneFeedbackMessage();
	                }

	                function MadoneFeedbackMessages( $params = null )
	                {
	                    $qs = new Storm_Queryset( 'MadoneFeedbackMessage' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadoneTempImage( $params = null )
	                {
	                    return $params ? new MadoneTempImage( $params ) : new MadoneTempImage();
	                }

	                function MadoneTempImages( $params = null )
	                {
	                    $qs = new Storm_Queryset( 'MadoneTempImage' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadoneTempFile( $params = null )
	                {
	                    return $params ? new MadoneTempFile( $params ) : new MadoneTempFile();
	                }

	                function MadoneTempFiles( $params = null )
	                {
	                    $qs = new Storm_Queryset( 'MadoneTempFile' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadoneShowcaseImage( $params = null )
	                {
	                    return $params ? new MadoneShowcaseImage( $params ) : new MadoneShowcaseImage();
	                }

	                function MadoneShowcaseImages( $params = null )
	                {
	                    $qs = new Storm_Queryset( 'MadoneShowcaseImage' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadoneShowcaseMovie( $params = null )
	                {
	                    return $params ? new MadoneShowcaseMovie( $params ) : new MadoneShowcaseMovie();
	                }

	                function MadoneShowcaseMovies( $params = null )
	                {
	                    $qs = new Storm_Queryset( 'MadoneShowcaseMovie' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                
	                function MadoneSubscriptionRecipient( $params = null )
	                {
	                    return $params ? new MadoneSubscriptionRecipient( $params ) : new MadoneSubscriptionRecipient();
	                }

	                function MadoneSubscriptionRecipients( $params = null )
	                {
	                    $qs = new Storm_Queryset( 'MadoneSubscriptionRecipient' );
	                    return $params ? $qs->filter( $params ) :  $qs;
	                }
	                