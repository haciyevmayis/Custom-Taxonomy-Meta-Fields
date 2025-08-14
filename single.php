<?php
// Mevcut kategori (taxonomy term) bilgisini al
$term = get_queried_object();

// Meta alanlarını çek
$content = get_term_meta( $term->term_id, 'content', true );
$image = get_term_meta( $term->term_id, 'image', true );

// Görsel varsa göster
if ( $image ) {
    echo '<div class="category-image">';
    echo wp_get_attachment_image( $image, 'full' );
    echo '</div>';
}

// İçerik varsa göster
if ( $content ) {
    echo '<div class="category-content">';
    echo wp_kses_post( $content );
    echo '</div>';
}
?>
