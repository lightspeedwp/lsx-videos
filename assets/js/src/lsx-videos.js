/**
 * LSX Videos scripts.
 *
 * @package    lsx-videos
 * @subpackage scripts
 */

var lsx_videos = Object.create( null );

;( function( $, window, document, undefined ) {

	'use strict';

	lsx_videos.document = $( document );
	lsx_videos.window = $( window );
	lsx_videos.window_height = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
	lsx_videos.window_width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

	/**
	 * Init videos widget/shotcode slider.
	 *
	 * @package    lsx-videos
	 * @subpackage scripts
	 */
	lsx_videos.init_slider = function() {
		$( '.lsx-videos-slider' ).each( function( index, el ) {
			var $video_slider = $( this );

			$video_slider.on( 'init', function( event, slick ) {
				if ( slick.options.arrows && slick.slideCount > slick.options.slidesToShow ) {
					$video_slider.addClass( 'slick-has-arrows' );
				}
			} );

			$video_slider.on( 'setPosition', function( event, slick ) {
				if ( ! slick.options.arrows ) {
					$video_slider.removeClass( 'slick-has-arrows' );
				} else if ( slick.slideCount > slick.options.slidesToShow ) {
					$video_slider.addClass( 'slick-has-arrows' );
				}
			} );

			$video_slider.slick( {
				draggable: false,
				infinite: true,
				swipe: false,
				cssEase: 'ease-out',
				dots: true,
				responsive: [{
					breakpoint: 992,
					settings: {
						slidesToShow: 3,
						slidesToScroll: 3,
						draggable: true,
						arrows: false,
						swipe: true
					}
				}, {
					breakpoint: 768,
					settings: {
						slidesToShow: 1,
						slidesToScroll: 1,
						draggable: true,
						arrows: false,
						swipe: true
					}
				}]
			} );
		} );
	};

	/**
	 * Adds modal effect to open single videos.
	 *
	 * @package    lsx-videos
	 * @subpackage scripts
	 */
	lsx_videos.init_modal = function() {
		$( '#lsx-videos-modal' ).on( 'show.bs.modal', function( event ) {
			var $modal = $( this ),
				$invoker = $( event.relatedTarget );

			$modal.find( '.modal-title' ).html( $invoker.data( 'title' ) );
			$modal.find( '.modal-body' ).html( '<div class="alert alert-info">Loading...</div>' );
		} );

		$( '#lsx-videos-modal' ).on( 'shown.bs.modal', function( event ) {
			var $modal = $( this ),
				$invoker = $( event.relatedTarget );

			$.ajax( {
				url: lsx_videos_params.ajax_url,

				data: {
					action: 'get_video_embed',
					video: $invoker.data( 'video' ),
					post_id: $invoker.data( 'post-id' )
				},

				success: function( data, textStatus, jqXHR ) {
					$modal.find( '.modal-body' ).html( data );
				},

				error: function( textStatus, jqXHR, errorThrown ) {
					$modal.find( '.modal-body' ).html( '<div class="alert alert-danger">Error!</div>' );
				}
			} );
		} );

		$( '#lsx-videos-modal' ).on( 'hidden.bs.modal', function( event ) {
			var $modal = $( this );

			$modal.find( '.modal-title' ).html( '' );
			$modal.find( '.modal-body' ).html( '' );
		} );
	};

	/**
	 * Adds modal effect to open single videos.
	 *
	 * @package    lsx-videos
	 * @subpackage scripts
	 */
	lsx_videos.featured_video_player = function() {
		var featuredDiv = jQuery( '.featured-video-section' ).length;
		if ( 0 < featuredDiv.length ) {	
			//watch the featured play button
			lsx_videos.watchPlayVideoButton();
		}
	}	

	/**
	 * Loads the video clicked into the player
	 *
	 * @package    lsx-videos
	 * @subpackage scripts
	 */
	lsx_videos.watchPlayVideoButton = function() {
		jQuery( '.lsx-video-play-btn' ).on( 'click', function() {
			jQuery( '.lsx-video-play-btn.paused' ).removeClass( 'paused' );

			var url = jQuery( this ).attr( 'data-video' );
			if ( ! jQuery( this ).hasClass( 'main-btn' ) ) {
				jQuery( '.lsx-video-play-btn' ).addClass( 'paused' );
			}
			lsx_videos.playVideo( url );
		});
	}	

	/**
	 * Loads the video clicked into the player
	 *
	 * @package    lsx-videos
	 * @subpackage scripts
	 */
	lsx_videos.playVideo = function( url ) {
		jQuery( '.featured-video-section .video-player' ).html( lsx_videos.videoIframe( url ) );
	}	

	/**
	 * Returns the iframe html
	 *
	 * @package    lsx-videos
	 * @subpackage scripts
	 */	
	lsx_videos.videoIframe = function( url ) {
		return '<iframe width="100%" height="100%" src="' + url + '" />';
	}

	/**
	 * On document ready.
	 *
	 * @package lsx-videos
	 * @subpackage scripts
	 */
	lsx_videos.document.ready( function() {
		lsx_videos.init_slider();
		lsx_videos.init_modal();

		lsx_videos.featured_video_player();
	} );

} )( jQuery, window, document );
