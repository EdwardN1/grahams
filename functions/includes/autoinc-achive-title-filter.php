<?php
function hap_hide_the_archive_title( $title ) {
    // Skip if the site isn't LTR, this is visual, not functional.
    // Should try to work out an elegant solution that works for both directions.
    if ( is_rtl() ) {
        return $title;
    }
    // Split the title into parts so we can wrap them with spans.
    $title_parts = explode( ': ', $title, 2 );
    // Glue it back together again.
    if ( ! empty( $title_parts[1] ) ) {
        $title = wp_kses(
            $title_parts[1],
            array(
                'span' => array(
                    'class' => array(),
                ),
            )
        );
        $title = '<span class="screen-reader-text is-hidden">' . esc_html( $title_parts[0] ) . ': </span>' . $title;
    }
    return $title;
}
add_filter( 'get_the_archive_title', 'hap_hide_the_archive_title' );