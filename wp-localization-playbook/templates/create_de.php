<?php
/**
 * Create a DE translation of a WordPress post and link via Polylang.
 *
 * Usage:
 *   1. Set $en_id and the path to de_*.html
 *   2. Adjust $title and $locale ('de', 'fr', 'es', etc.)
 *   3. Run: wp eval-file create_de.php
 *   4. Clean up: rm create_de.php de_*.html on server
 */

$en_id = 12345;
$locale = 'de';

$html_file = __DIR__ . "/de_{$en_id}.html";
if (!file_exists($html_file)) {
    echo "ERROR: {$html_file} not found\n";
    exit(1);
}
$content = file_get_contents($html_file);

$post = get_post($en_id);
$title = ''; // Set the translated title here

$de_id = wp_insert_post([
    'post_title'   => $title,
    'post_content' => $content,
    'post_status'  => 'publish',
    'post_type'    => $post->post_type,
    'post_author'  => 1,
], true);

if (is_wp_error($de_id)) {
    echo "ERROR: " . $de_id->get_error_message() . "\n";
    exit(1);
}
echo "Created DE post ID: {$de_id}\n";

pll_set_post_language($de_id, $locale);
pll_save_post_translations(['en' => $en_id, 'de' => $de_id]);
echo "Translation group saved (en={$en_id}, de={$de_id})\n";

$en_thumb = get_post_thumbnail_id($en_id);
if ($en_thumb) {
    set_post_thumbnail($de_id, $en_thumb);
    echo "Thumbnail copied (id={$en_thumb})\n";
}

echo "DE slug: " . get_post_field('post_name', $de_id) . "\n";