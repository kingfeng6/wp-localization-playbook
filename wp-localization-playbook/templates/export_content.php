<?php
/**
 * Batch-export WordPress posts to local HTML files.
 *
 * Usage:
 *   1. Set $post_ids below
 *   2. Run via WP-CLI: wp eval-file export_content.php
 *   3. Output written to CWD as batch_*.txt (one block per post)
 *
 * Output format per post:
 *   ======ID:{en_id}======
 *   {title}
 *   ---THUMB---
 *   {thumb_id}
 *   ---CONTENT---
 *   {post_content}
 */

$post_ids = [
    // 16172, 16125, 16139, ...
];

$output = '';
foreach ($post_ids as $id) {
    $post = get_post($id);
    if (!$post) {
        echo "WARN: post {$id} not found\n";
        continue;
    }
    $thumb_id = get_post_thumbnail_id($id);
    $output .= "=====ID:{$id}=====\n";
    $output .= $post->post_title . "\n";
    $output .= "---THUMB---\n";
    $output .= ($thumb_id ?: 'none') . "\n";
    $output .= "---CONTENT---\n";
    $output .= $post->post_content . "\n";
}

$ts = date('Ymd-His');
$filename = __DIR__ . "/batch_{$ts}.txt";
file_put_contents($filename, $output);
echo "Exported " . count($post_ids) . " posts to {$filename}\n";